  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{url('/dropshipper/dashboard')}}" class="brand-link">
      <img src="https://webcoder-it.com/frontend/assets/images/logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Dropshipper</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-book"></i>
              <p>
                Orders
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('/dropshipper/orders/pending')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Pending</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/dropshipper/orders/complete')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Shipment</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/dropshipper/orders/delivered')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Delivered</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/dropshipper/orders/return')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Returned</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('/dropshipper/orders/all')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>All Orders</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-book"></i>
              <p>
                Transactions
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('dropshipper/deposit-history')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Deposit History</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('dropshipper/withdraw-history')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Withdraw History</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>