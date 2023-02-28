const {
  default: makeWASocket,
MessageType, 
  MessageOptions, 
  Mimetype,
DisconnectReason,
BufferJSON,
  AnyMessageContent, 
delay, 
fetchLatestBaileysVersion, 
isJidBroadcast, 
makeCacheableSignalKeyStore, 
makeInMemoryStore, 
MessageRetryMap, 
useMultiFileAuthState,
msgRetryCounterMap
} =require("@adiwajshing/baileys");

const log = (pino = require("pino"));
const { session } = {"session": "session_info"};
const { Boom } =require("@hapi/boom");
const path = require('path');
const fs = require('fs');
const http = require('http');
const https = require('https');
const express = require("express");
const fileUpload = require('express-fileupload');
const cors = require('cors');
const bodyParser = require("body-parser");
const app = require("express")()
// enable files upload
app.use(fileUpload({
  createParentPath: true
}));

app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({extended: true}));
const server = require("http").createServer(app);
const io = require("socket.io")(server);
const port = process.env.PORT || 3000;
const qrcode = require("qrcode");

app.use("/assets", express.static(__dirname + "/client/assets"));

app.get("/scan", (req, res) => {
res.sendFile("./client/server.html", {
  root: __dirname,
});
});

app.get("/", (req, res) => {
res.sendFile("./client/index.html", {
  root: __dirname,
});
});
const store = makeInMemoryStore({ logger: pino().child({ level: "silent", stream: "store" }) });

let sock;
let qr;
let soket;

async function connectToWhatsApp() {
const { state, saveCreds } = await useMultiFileAuthState('session_info')
let { version, isLatest } = await fetchLatestBaileysVersion();
  sock = makeWASocket({
      printQRInTerminal: true,
  auth: state,
  logger: log({ level: "silent" }),
  version,
  shouldIgnoreJid: jid => isJidBroadcast(jid),
  });
store.bind(sock.ev);
sock.multi = true
sock.ev.on('connection.update', async (update) => {
    //console.log(update);
  const { connection, lastDisconnect } = update;
  if(connection === 'close') {
          let reason = new Boom(lastDisconnect.error).output.statusCode;
    if (reason === DisconnectReason.badSession) {
      console.log(`Bad Session File, Please Delete ${session} and Scan Again`);
      sock.logout();
    } else if (reason === DisconnectReason.connectionClosed) {
      console.log("Connection closed, reconnecting...");
      connectToWhatsApp();
    } else if (reason === DisconnectReason.connectionLost) {
      console.log("Connection Lost from Server, reconnecting...");
      connectToWhatsApp();
    } else if (reason === DisconnectReason.connectionReplaced) {
      console.log("Connection Replaced, Another New Session Opened, Please Close Current Session First");
      sock.logout();
    } else if (reason === DisconnectReason.loggedOut) {
      console.log(`Device Logged Out, Please Delete ${session} and Scan Again.`);
      sock.logout();
    } else if (reason === DisconnectReason.restartRequired) {
      console.log("Restart Required, Restarting...");
      connectToWhatsApp();
    } else if (reason === DisconnectReason.timedOut) {
      console.log("Connection TimedOut, Reconnecting...");
      connectToWhatsApp();
    } else {
      sock.end(`Unknown DisconnectReason: ${reason}|${lastDisconnect.error}`);
    }
      }else if(connection === 'open') {
    console.log('opened connection');
    let getGroups = await sock.groupFetchAllParticipating();
    let groups = Object.entries(getGroups).slice(0).map(entry => entry[1]);
    console.log(groups);
    return;
      } 
  });
sock.ev.on("creds.update", saveCreds);
sock.ev.on("messages.upsert", async ({ messages, type }) => {
      //console.log(messages);
      if(type === "notify"){
          if(!messages[0].key.fromMe) {
              //tentukan jenis pesan berbentuk text                
              const pesan = messages[0].message.conversation;
      
      //nowa dari pengirim pesan sebagai id
              const noWa = messages[0].key.remoteJid;

              await sock.readMessages([messages[0].key]);

              //kecilkan semua pesan yang masuk lowercase 
              const pesanMasuk = pesan.toLowerCase();
      
              if(!messages[0].key.fromMe && pesanMasuk === "test"){
                  await sock.sendMessage(noWa, {text: "Haloo"},{quoted: messages[0] });
              }else{
                  await sock.sendMessage(noWa, {text: "Mohon untuk tidak membalas nomor ini. Terimakasih! *Pesan dikirim oleh bot."},{quoted: messages[0] });
              }
    }		
  }		
  });
}

io.on("connection", async (socket) => {
  soket = socket;
  // console.log(sock)
  if (isConnected) {
      updateQR("connected");
  } else if (qr) {
      updateQR("qr");   
  }
});

// functions
const isConnected = () => {
  return (sock.user);
};

const updateQR = (data) => {
  switch (data) {
      case "qr":
          qrcode.toDataURL(qr, (err, url) => {
              soket?.emit("qr", url);
              soket?.emit("log", "QR Code received, please scan!");
          });
          break;
      case "connected":
          soket?.emit("qrstatus", "./assets/check.svg");
          soket?.emit("log", "WhatsApp terhubung!");
          break;
      case "qrscanned":
          soket?.emit("qrstatus", "./assets/check.svg");
          soket?.emit("log", "QR Code Telah discan!");
          break;
      case "loading":
          soket?.emit("qrstatus", "./assets/loader.gif");
          soket?.emit("log", "Registering QR Code , please wait!");
          break;
      default:
          break;
  }
};

// send text message to wa user
app.post("/send", async (req, res) =>{
  //console.log(req);
  const msg = req.body.message;
  const number = req.body.number;
  const fileSent = req.files;
  
let numberWA;
  try {
      if(!req.files) 
      {
          if(!number) {
               res.status(500).json({
                  status: false,
                  response: 'Please input WhatsApp Number!'
              });
          }
          else
          {
              numberWA = '62' + number + "@s.whatsapp.net"; 
              console.log(await sock.onWhatsApp(numberWA));
              if (isConnected) {
                  const exists = await sock.onWhatsApp(numberWA);
                  if (exists?.jid || (exists && exists[0]?.jid)) {
                      sock.sendMessage(exists.jid || exists[0].jid, { text: msg })
                      .then((result) => {
                          res.status(200).json({
                              status: true,
                              response: result,
                          });
                      })
                      .catch((err) => {
                          res.status(500).json({
                              status: false,
                              response: err,
                          });
                      });
                  } else {
                      res.status(500).json({
                          status: false,
                          response: `${number} is not registered.`,
                      });
                  }
              } else {
                  res.status(500).json({
                      status: false,
                      response: `WhatsApp session is not connected.`,
                  });
              }    
          }
      }
      else
      {
          //console.log('Kirim document');
          if(!number) {
               res.status(500).json({
                  status: false,
                  response: 'Please input WhatsApp Number!'
              });
          }
          else
          {
              
              numberWA = '62' + number + "@s.whatsapp.net"; 
              //console.log('Kirim document ke'+ numberWA);
              let filesave = req.files.file_sent;
              var file_change_name = new Date().getTime() +'_'+filesave.name;
              //pindahkan file ke dalam upload directory
              filesave.mv('./uploads/' + file_change_name);
              let fileSent_Mime = filesave.mimetype;
              //console.log('Simpan document '+fileSent_Mime);

              //console.log(await sock.onWhatsApp(numberWA));

              if (isConnected) {
                  const exists = await sock.onWhatsApp(numberWA);

                  if (exists?.jid || (exists && exists[0]?.jid)) {
                      
                      let namafileSent = './uploads/' + file_change_name;
                      let extensionName = path.extname(namafileSent); 
                      //console.log(extensionName);
                      if( extensionName === '.jpeg' || extensionName === '.jpg' || extensionName === '.png' || extensionName === '.gif' ) {
                           await sock.sendMessage(exists.jid || exists[0].jid, { 
                              image: {
                                  url: namafileSent
                              },
                              caption:msg
                          }).then((result) => {
                              if (fs.existsSync(namafileSent)) {
                                  fs.unlink(namafileSent, (err) => {
                                      if (err && err.code == "ENOENT") {
                                          // file doens't exist
                                          console.info("File does not exist.");
                                      } else if (err) {
                                          console.error("Error occurred while trying to remove file.");
                                      }
                                      //console.log('File deleted!');
                                  });
                              }
                              res.send({
                                  status: true,
                                  message: 'Success',
                                  data: {
                                      name: filesave.name,
                                      mimetype: filesave.mimetype,
                                      size: filesave.size
                                  }
                              });
                          }).catch((err) => {
                              res.status(500).json({
                                  status: false,
                                  response: err,
                              });
                              console.log('Message failed to send.');
                          });
                      }else if(extensionName === '.mp3' || extensionName === '.ogg'  ) {
                          await sock.sendMessage(exists.jid || exists[0].jid, { 
                             audio: { 
                                  url: namafileSent,
                                  caption: msg 
                              }, 
                              mimetype: 'audio/mp4'
                          }).then((result) => {
                              if (fs.existsSync(namafileSent)) {
                                  fs.unlink(namafileSent, (err) => {
                                      if (err && err.code == "ENOENT") {
                                          // file doens't exist
                                          console.info("File does not exist.");
                                      } else if (err) {
                                          console.error("Error occurred while trying to remove file.");
                                      }
                                      //console.log('File deleted!');
                                  });
                              }
                              res.send({
                                  status: true,
                                  message: 'Success',
                                  data: {
                                      name: filesave.name,
                                      mimetype: filesave.mimetype,
                                      size: filesave.size
                                  }
                              });
                          }).catch((err) => {
                              res.status(500).json({
                                  status: false,
                                  response: err,
                              });
                              console.log('Message failed to send.');
                          });
                      }else {
                          await sock.sendMessage(exists.jid || exists[0].jid, {
                              document: { 
                                  url:  namafileSent,
                                  caption: msg 
                              }, 
                              mimetype: fileSent_Mime,
                              fileName: filesave.name
                          }).then((result) => {
                              if (fs.existsSync(namafileSent)) {
                                  fs.unlink(namafileSent, (err) => {
                                      if (err && err.code == "ENOENT") {
                                          // file doens't exist
                                          console.info("File does not exist.");
                                      } else if (err) {
                                          console.error("Error occurred while trying to remove file.");
                                      }
                                      //console.log('File deleted!');
                                  });
                              }
                              /*
              setTimeout(() => {
                                  sock.sendMessage(exists.jid || exists[0].jid, {text: msg});
                              }, 1000);
              */
                              res.send({
                                  status: true,
                                  message: 'Success',
                                  data: {
                                      name: filesave.name,
                                      mimetype: filesave.mimetype,
                                      size: filesave.size
                                  }
                              });
                          }).catch((err) => {
                              res.status(500).json({
                                  status: false,
                                  response: err,
                              });
                              console.log('Message failed to send.');
                          });
                      }
                  } else {
                      res.status(500).json({
                          status: false,
                          response: `${number} is not registered.`,
                      });
                  }
              } else {
                  res.status(500).json({
                      status: false,
                      response: `WhatsApp session is not connected.`,
                  });
              }    
          }
      }
  } catch (err) {
      res.status(500).send(err);
  }
  
});

connectToWhatsApp()
.catch (err => console.log("unexpected error: " + err) ) // catch any errors
server.listen(port, () => {
console.log("Server running on Port : " + port);
});
