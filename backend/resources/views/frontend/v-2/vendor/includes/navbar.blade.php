<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
      <li class="nav-item">
          <a class="nav-link toggle-btn-outer" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>

  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">

      <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
              <i class="fas fa-expand-arrows-alt"></i>
          </a>
      </li>

      <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
              <i class="fas fa-user-circle"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
              <a href="#" class="dropdown-item">
                  Profile
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                          document.getElementById('logout-form').submit();">
                      {{ __('Logout') }}
                  </a>

                  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                      @csrf
                  </form>
          </div>
      </li>

  </ul>
</nav>