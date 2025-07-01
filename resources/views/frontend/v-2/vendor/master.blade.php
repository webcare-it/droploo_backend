<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dropshipper | Dashboard</title>

  @include('frontend.v-2.vendor.includes.style')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  @include('frontend.v-2.vendor.includes.navbar')

  @include('frontend.v-2.vendor.includes.sidebar')

  <div class="content-wrapper">
    @yield('content')
  </div>

  @include('frontend.v-2.vendor.includes.footer')
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

@include('frontend.v-2.vendor.includes.script')

@stack('script')
</body>
</html>