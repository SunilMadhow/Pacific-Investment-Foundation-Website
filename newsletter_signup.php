<!DOCTYPE html>
<head>
	<title>Pacific Investment Foundation</title>
	<link rel="stylesheet" type="text/css" href="newsletter_signup.css">
	<!--bootstrap and stuff STOP REMOVING IT-->
	</head>
<body>
  <h1>Follow our <i>Journey</i></h1>
  <form action="newsletter_signup.php" method="get">
    <?php
      $email_exists = array_key_exists("email", $_GET);
			$lines = 0;
      if(!$email_exists){
        //If email is blank, user hasn't pressed submit yet
        echo_form($lines);
      } else {
        //Email is set
        $email = $_GET["email"];
        //Test if it is a valid email.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          //Give error if not
          echo "<span class='error'>".$email." is not a valid email address.</span>";
          //Let user resubmit form
					$lines++;
          echo_form($lines);
          //Work here is done.
        } else {
          $servername = "localhost";
          $username = "newsletter";
          $password = "qwerty%1234!";
          $dbname = "newsletter_users";

          $sql_hook = new mysqli($servername, $username, $password, $dbname);
          //Passed valid email test
          if(mysqli_real_escape_string($sql_hook, $email) != $email){
            echo "<span class='error'>Critical Error</span>";
            //Person is trying to sql inject us.  We must punish them by not letting them see the submit box!
            $sql_hook->close();
            //Do nothing so as to confuse hacker!!!11!1!eleven!!
            //Work here is done.
          } else {
            //SQL safe.  Now check SQL Database to see if this email already exists;
            if(email_is_in_database($sql_hook, $email)){
              $sql_hook->close();
              //Email has already been submitted...
              echo "<span class='error'>". $email . " has already been subscribed to the newsletter.</span>";
							$lines++;
							//Allow user to resubmit
              echo_form($lines);
              //Our work here is done.
            } else {

              //echo "All good to add!<br>";
              //Email is free to add to the database.
              add_email_to_database($sql_hook, $email);
              echo "<span class='success'>Your email was added sucessfully!</span>";
              //That's all folks!
            }
          }
        }
      }
      //4 nested if's... not bad!
      function email_is_in_database($sql_hook, $email){
        // Check connection
        if ($sql_hook->connect_error) {
          echo "<span class='error'>We can't add your email at this time.  Please try again later.</span>";
          //die isn't pretty but we need it in this case :(
          die("Connection failed: " . $sql_hook->connect_error);
        }

        $sql = "SELECT id FROM newsletter_users WHERE email='" . $email . "'";

        //TODO Debug:
        //echo "SQL: " . $sql . "<br>";

        //Execute!
        $result = $sql_hook->query($sql);

        //echo "Result: " . $result->num_rows ."<br>";

        //Don't close $sql_hook until we are done with it!
        //If we have 1 or more rows with that email, then the email is already in the database.
        return $result->num_rows > 0;

      }
      function add_email_to_database($sql_hook, $email){
        // Check connection
        if ($sql_hook->connect_error) {
          echo "<span class='error'>We can't add your email at this time.  Please try again later.</span>";
          //die isn't pretty but we need it in this case :(
          die("Connection failed: " . $sql_hook->connect_error);
        }

        //Just good to have...
        $user_ip = $_SERVER['REMOTE_ADDR'];
				$unique_code = get_unique_code($sql_hook);

        $sql = "INSERT INTO newsletter_users (email, ip, emailSubscriptionLevel, code) VALUES ('$email', '$user_ip',0,'$unique_code')";


        //TODO Debug:
        //echo "SQL: " . $sql . "<br>";

        if ($sql_hook->query($sql) === TRUE) {
          //TODO Debug:
          //echo "New record created successfully";
        } else {
          //TODO Debug:
          echo "Error: " . $sql . "<br>" . $sql_hook->error;
        }

        //Close $sql_hook as we are done with it!
        $sql_hook->close();

        //Now, THAT was fun.  Woohoo!
      }

			function get_unique_code($sql_hook){
				//Repeat until code found
				while(true){
					//Code is a random int between 10^3 and 10^6
					$code = mt_rand(1000,1000000);

					//Verify code is not used:
					// Check connection
	        if ($sql_hook->connect_error) {
	          echo "<span class='error'>We can't add your email at this time.  Please try again later.</span>";
	          //die isn't pretty but we need it in this case :(
	          die("Connection failed: " . $sql_hook->connect_error);
	        }

	        $sql = "SELECT id FROM newsletter_users WHERE code=" . $code;

	        //Execute!
	        $result = $sql_hook->query($sql);

	        //echo "Result: " . $result->num_rows ."<br>";

	        //Don't close $sql_hook until we are done with it!
	        //No rows => unique code
	        if($result->num_rows == 0){
						return $code;
					}
				}
			}
      function echo_form(){
        echo "
				<div id='container'>
          <div id='email_container'>
            <input type='email' name='email' placeholder='Email Address'/>
          </div>
          <input type='submit' onclick='//TODO:resizeIframe();' value='Subscribe Now'/>
        </div>
				";
      }
    ?>

  </form>
</body>
