<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tracking Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
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
            <form method="POST" action="/tracking/detail" class="my-auto">
                {{ csrf_field() }}
                <div class="input-group">
                    <input type="text" name="tracking_no" class="form-control" placeholder="Insert Phone Number/Reparation No." value="{{ old('tracking_no') }}">
                    <button type="submit" class="btn btn-success">Track</button>
                </div>
            </form>
        </div>
        <h2>Reparation Detail</h2>
        <div>
            <table class="col-md-12 text-center">
                <tr>
                    <td>
                        Reparation No.
                    </td>
                    <td>
                        {{ $reparation_id }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Name
                    </td>
                    <td>
                        {{ $csname }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Phone
                    </td>
                    <td>
                        {{ '+62'.$csphone }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Brand
                    </td>
                    <td>
                        {{ $cbrand }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Type
                    </td>
                    <td>
                        {{ $ctype }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Serial Number
                    </td>
                    <td>
                        {{ $csn }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Problem
                    </td>
                    <td>
                        {{ $cprob }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Equipment
                    </td>
                    <td>
                        @if($cbag==1 && $ccharger==1)
                        Bag, Charger Cable
                        @elseif($ccharger==1)
                        Charger Cable
                        @elseif($cbag==1)
                        Bag
                        @else
                        -
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>
                        Status
                    </td>
                    <td>
                        @if(!$inspection_date)
                        On queue for checking
                        @elseif($repair_agree == 0)
                        Reparation Cancelled
                        @elseif($repair_finish)
                        On QC inspection at {{ $repair_finish }}
                        @elseif($repair_start)
                        On repair
                        @elseif($inspection_date)
                        Waiting for repair confirmation
                        @endif
                    </td>
                </tr>
            </table>
        </div>


    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>