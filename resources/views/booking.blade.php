<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Book a Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
          <a class="navbar-brand mx-auto" href="#">Bigbang Computer</a>
          
          </div>
        </div>
      </nav>
    <div class="container">
        <div class="form-group my-5">
            <h4 class="text-center">Book a Service</h4>
            <form method="POST" action="/booking/book" class="row my-auto">
                {{ csrf_field() }}
                <div class="my-3 col-md-6">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Name" required>
                </div>
                <div class="my-3 col-md-6">
                    <label for="phone">WhatsApp No.</label>
                    <div class="input-group">
                        <span class="input-group-text">+62</span>
                        <input type="text" name="phone" class="form-control" placeholder="8xxxxx" required>
                    </div>
                </div>
                <div class="my-3 col-md-6">
                    <label for="brand">Brand</label>
                    <input type="text" name="brand" class="form-control" placeholder="Brand" required>
                </div>
                <div class="my-3 col-md-6">
                    <label for="type">Type</label>
                    <input type="text" name="type" class="form-control" placeholder="Type" required>
                </div>
                <div class="my-3 col-md-6">
                    <label for="serial_number">Serial Number</label>
                    <input type="text" name="serial_number" class="form-control" placeholder="Serial Number">
                </div>
                <div class="my-3 col-md-6">
                    <label for="problem">Problem</label>
                    <input type="text" name="problem" class="form-control" placeholder="Problem" required>
                </div>
                <div class="my-3">
                    <p>Equipment</p>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="eq_bag" value="1">
                        <label class="form-check-label" for="eq_bag">Bag</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="eq_charger_cable" value="1">
                        <label class="form-check-label" for="eq_bag">Charger/Power Cable</label>
                    </div>
                </div>
                <div class="my-3">
                    <label for="book_date">Booking Date</label><br>
                    <input type="text" name="book_date" id="book_date">
                </div>
                <button type="submit" class="btn btn-success mx-auto">Book</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script>
    $(function() {
      // Initialize datepicker
      $('#book_date').datepicker({
        format: 'dd-mm-yyyy',
      });
    });
  </script>
</body>
</html>