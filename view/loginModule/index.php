<?php
session_start();

if (isset($_SESSION['studentSRCode'])) {
  header('Location: ../../view/student_view.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BatStateU Faculty Evaluation</title>
  <link rel="shortcut icon" href="../../public/picture/cics.png" type="image/x-icon" />
  <link rel="stylesheet" href="../../public/css/login.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- JQUERY CDN -->
  <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
  <!--  -->

  <!-- LOCALSTORAGE JS -->
  <script src="../../public/js/localStorage.js"></script>
  <!--  -->
</head>

<body>
  <main>
    <div class="box">
      <div class="inner-box">
        <div class="forms-wrap">
          <form action="../../controller/login.php" method="POST" autocomplete="off" class="sign-in-form">
            <div class="logo">
              <img src="../../public/picture/bsu.png" alt="BatStateU-Logo" />
              <img src="../../public/picture/cics.png" alt="CICS-Logo" />
            </div>

            <div class="heading">
              <h2>Student Login</h2>
              <h6>Are you a Faculty?</h6>
              <a href="#" class="toggle">Click here</a>
            </div>

            <div class="actual-form">
              <div class="input-wrap">
                <input type="text" name="studentSRCode" id="studentSRCode" minlength="4" class="input-field" autocomplete="off" required />
                <label>SR-Code</label>
              </div>

              <div class="input-wrap">
                <input type="password" name="studentpass" minlength="4" id="passStudentInput" class="input-field"
                  autocomplete="off" required />
                <label>Password</label>
                <span class="password-toggle-icon"><i class="fa-solid fa-eye" id="passStudent"></i></span>
              </div>

              <input type="submit" name="studentLogin" id="studentLogin" value="Sign In" class="sign-btn" />

            </div>
          </form>

          <form action="../../controller/login.php" method="POST" autocomplete="off" class="sign-up-form">
            <div class="logo">
              <img src="../../public/picture/bsu.png" alt="BatStateU-Logo" />
              <img src="../../public/picture/cics.png" alt="CICS-Logo" />
            </div>

            <div class="heading">
              <h2>Faculty Login</h2>
              <h6>Are you a Student?</h6>
              <a href="#" class="toggle">Click here</a>
            </div>

            <div class="actual-form">

              <div class="input-wrap">
                <input type="text" class="input-field" autocomplete="off" name="gsuite" required />
                <label>Gsuite</label>
              </div>

              <div class="input-wrap">
                <input type="password" name="password" minlength="2" id="passFacultyInput" class="input-field"
                  autocomplete="off" required />
                <label>Password</label>
                <span class="password-toggle-icon"><i class="fa-solid fa-eye" id="passFaculty"></i></span>
              </div>

              <input type="submit" name="facultyadmin" value="Sign In" class="sign-btn" />

            </div>
          </form>
        </div>

        <div class="carousel">
          <div class="images-wrapper">
            <img src="../../public/picture/BatStateU-cover-1.jpg" class="image img-1 show" alt />
            <img src="../../public/picture/BatStateU-cover-2.jpg" class="image img-2" alt />
            <img src="../../public/picture/BatStateU-cover-3.jpg" class="image img-3" alt />
          </div>

          <div class="text-slider">
            <div class="text-wrap">
              <div class="text-group">
                <h2>Leading innovations,</h2>
                <h2>Transforming Lives,</h2>
                <h2>Building the Nation.</h2>
              </div>
            </div>

            <div class="bullets">
              <span class="active" data-value="1"></span>
              <span data-value="2"></span>
              <span data-value="3"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Javascript file -->
  <script src="../../public/js/jquery-3.7.1.min.js"></script>

  <script>
    $(document).ready(function () {

      // Show password for Student 
      $('#passStudent').click(function () {
        $(this).toggleClass("fa-eye fa-eye-slash");
        $("#passStudentInput").attr('type', $("#passStudentInput").attr('type') === 'password' ? 'text' : 'password');
      })

      // Show password for Faculty
      $('#passFaculty').click(function () {
        $(this).toggleClass("fa-eye fa-eye-slash");
        $("#passFacultyInput").attr('type', $("#passFacultyInput").attr('type') === 'password' ? 'text' : 'password');
      })

    })
  </script>
  <script src="../../public/js/main.js"></script>
  <script src="../../public/js/sweetalert.min.js"></script>

  <?php
  if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
    ?>
    <script>
      swal({
        title: "<?php echo $_SESSION['status']; ?>",
        // text: "You clicked the button!",
        icon: "<?php echo $_SESSION['status-code']; ?>",
        button: "Ok",
      });
    </script>
    <?php
    unset($_SESSION['status']);
  }
  ?>
</body>

</html>