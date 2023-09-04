<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container">
    <br>
    <h2>Employee User Management</h2>

    <form id="insertForm">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="employee_name" class="form-control" />
            </div>
            <div class="col-md-4">
                <input type="number" name="salary" class="form-control" />
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Insert</button>
            </div>
        </div>
    </form>
    <br>
    <form id="searchForm">
        <div class="input-group">
        <div class="form-outline">
            <input type="text" id="search_employee" name="search_employee" class="form-control" />
        </div>
        <button type="submit" class="btn btn-primary">
            Search
        </button>
        </div>
    </form>
    </br>
    <table class="table">
        <thead>
            <th>Id</th>
            <th>Employee Name</th>
            <th>Salary</th>
            <th>Action</th>
        </thead>
        <tbody id="main_contents"></tbody>
    </table>
</div>
<script src="app.js"></script>
</body>
</html>