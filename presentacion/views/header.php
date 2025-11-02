<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php if(isset($this->title)) echo $this->title; ?></title>
 
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
 <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
      main > .container {
  padding: 60px 15px 0;
}

.footer {
  background-color: #f5f5f5;
}

.footer > .container {
  padding-right: 15px;
  padding-left: 15px;
}

code {
  font-size: 80%;
}
    </style>

    <title>Hello, world!</title>
  </head>

    <body>
    <header>
    <?php
       if (isset($_SESSION['level']) and $_SESSION['level'] == 2) {
       $href='tutor'; 
    } elseif (isset($_SESSION['level']) and $_SESSION['level'] == 1) {
        $href='student';
    }
      ?>
  <!-- Fixed navbar -->
  <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <a class="navbar-brand" href="index">TutorLink</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a class="nav-link" href="index">Home <span class="sr-only">(current)</span></a>
        </li> 
      </ul>
      <ul class="navbar-nav ml-auto">
        <?php if(!isset($_SESSION['approved'])) echo
       '<li class="nav-item">
          <a class="nav-link" href="'.BASE_URL.'user/handleLogin">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="'.BASE_URL.'user/handleRegistration">Registrarse</a>
        </li> ';
        ?>
        <?php if(isset($_SESSION['approved']) && $_SESSION['approved']==1) echo       
        '<li class="nav-item">
          <a class="nav-link" href="'.$href.'">Mi Perfil</a>
        </li> 
         <li class="nav-item">
          <a class="nav-link" href="'.BASE_URL.'user/close">Logout</a>
        </li>'?> 
      </ul>
    </div>
  </nav>
</header>
<main role="main" class="flex-shrink-0">
<div class="container">   
<?php if(isset($this->_message)):?>
  <div class="alert alert-secondary" role="alert"><?php echo $this->_message['msj'];?> </div>
<?php endif;?>         