<?php
	include($_SERVER['DOCUMENT_ROOT']."/Logistics/php/peopleDB.php");
	include($_SERVER['DOCUMENT_ROOT']."/Logistics/php/carsDB.php");
	class people
	{
		public $name;
		public $address;
		public $seated;
		public $carArray = array();
	}

	class cars
	{
		public $name;
		public $address;
		public $seats;
		public $pplArray = array();
	}

	$peopleArray = array();
	$carsArray = array();

	//save all the variables for the people objects
	$sqlPerson = "SELECT * FROM $table_people";
	$dataPerson = mysqli_query($connect_people, $sqlPerson);

	//while there are still rows in the person table
	while ($rowPerson = mysqli_fetch_array($dataPerson, MYSQLI_ASSOC))
	{
		//create an object person and then add it to the people array
		$addPerson = new people();
		$addPerson->name = $rowPerson['firstName'] . " " . 
			$rowPerson['lastName'];
		$addPerson->address = $rowPerson['address'];
		$addPerson->seated = false;
		$peopleArray[] = $addPerson;
	}

	//save all the variables for the car objects
	$sqlCar = "SELECT * FROM $table_cars";
	$dataCar = mysqli_query($connect_cars, $sqlCar);

	//while there are still rows in the car table
	while ($rowCar = mysqli_fetch_array($dataCar, MYSQLI_ASSOC))
	{
		//create a car object with data and add it to the cars array
		$addCar = new cars();
		$addCar->name = $rowCar['carName'];
		$addCar->address = $rowCar['address'];
		$addCar->seats = $rowCar['seats'];
		$carsArray[] = $addCar;
	}

	//cars are colored yellow and people grey all can be moved
	//print out the column of sortable grid for people
	printf('
		<h1>List of People</h1>
		<ul id="sortable" class="droptrue">
	');
	for ($p = 0; $p < sizeof($peopleArray); $p++)
	{
		printf('
			<li class="ui-state-default">%s</li>

		',$peopleArray[$p]->name
		);
	}

	printf('
		</ul>
		<div></div>
		<h1>List of Cars</h1>
		<ul id="sortable" class="droptrue">
	');
	for ($c = 0; $c < sizeof($carsArray); $c++)
	{
		printf('
			<li class="ui-state-highlight">%s</li>
		', $carsArray[$c]->name."'s Car"
		);
	}

	printf('
		</ul>
		<br></br>
	');

	for ($d = 0; $d < sizeof($carsArray); $d++)
	{
		printf('
			<ul id="sortable" class="droptrue">
			</ul>
		');
	}


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<title>Working on printing distances</title>
		<link rel="stylesheet" href="/Logistics/css/all.css" type="text/css"/>
		 <script>
			$(function() 
			{
				$("ul.droptrue").sortable(
				{
			    	connectWith: "ul"
			    });                                                                               

			 
			    $("#sortable").disableSelection();
			});
  </script>
	</head>
		<body>
		<div id = "roto">
		</div>
		<script async defer
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCypkWOjdFJdg24D3urWSodGjnt-Bi8hpo&callback=gmapsAPI">
	    </script>
		<script type="text/javascript">
		function gmapsAPI()
		{
			/*<?php
			for ($x = 0; $x < (sizeOf($carsArray)-1); $x++)
			{ ?>
				var outputLoc = document.getElementById("roto");
				outputLoc.innerHTML += ('<?=$x?>');
			<?php } ?>
			*/
			var outputLoc = document.getElementById("roto");

			var carCount = <?php echo json_encode(sizeof($carsArray))?>;
			var peopleCount =  <?php echo json_encode(sizeof($peopleArray))?>;
			var carDistances = []; //save the closest people to the car
			var carSeating = [];
			
			<?php
			for ($x = 0; $x < sizeOf($carsArray); $x++)
			{//iterate through all the cars to fill
			?>
				carDistances[<?=$x?>] = [];
				carSeating[<?=$x?>] = [];
				var seatsCount = <?php echo json_encode($carsArray[$x]->seats)?>;
				var peopleIterator = 0;
				for(var fill = 0; fill < seatsCount; fill++)
				{//fill the carSeating array with -1 to represent empty seats
				
					carDistances[<?=$x?>][fill] = -1;
				}

				<?php
				for ($y = 0; $y < sizeOf($peopleArray); $y++) 
				{//iterate through all the people in database 
				?>
						var service = new google.maps.DistanceMatrixService();
						service.getDistanceMatrix(
						{
							origins: [<?php echo json_encode($carsArray[$x]->address) ?>],//car location
							destinations: [<?php echo json_encode($peopleArray[$y]->address) ?>],//people address
							travelMode: google.maps.TravelMode.DRIVING,
							unitSystem: google.maps.UnitSystem.METRIC,
							avoidHighways: false,
							avoidTolls: false,
						}, function callback(response, status)
						{
							var tempDist = 0.0;//save the distance of one car to person
							if (status !== google.maps.DistanceMatrixStatus.OK) 
							{
	            				alert('Error was: ' + status);
	         				} else 
	         				{
		         				var originList = response.originAddresses;
		         				var destinationList = response.destinationAddresses;
		         				//alert(originList.length);
		         				for (var i = 0; i < originList.length; i++)
		         				{
		         					var results = response.rows[i].elements;
									//alert(results.length);
		         					for (var j = 0; j < results.length; j++) 
		         					{
		         						tempDist = results[j].distance.value;
		         						console.log(
		         							<?php echo json_encode($peopleArray[$y]->name) ?>
		         							+ ": " + tempDist);
		         						//alert(tempDist);
		         						//outputLoc.innerHTML += tempDist;
		         					}
								}
							}
							peopleIterator++;
							var decrementIndex = seatsCount - 1;
							//INSERT INTO THE MULTIDEMINSIONAL ARRAYS AHHH

							/* add functionality of subtracting the people who have already been chosen in while loop
							<?php echo json_encode($peopleArray[$y]->seated)?> == false &&
							*/
							while (decrementIndex >= 0 && 
								  (tempDist < carDistances[<?=$x?>][decrementIndex] || carDistances[<?=$x?>][decrementIndex] == -1))
							{
								var tempSwap = carDistances[<?=$x?>][decrementIndex];
								var tempName = carSeating[<?=$x?>][decrementIndex];
								//ADD FUNCTIONALITY SAVE INDEX OF PERSON IN CAR SEATING SO YOU CAN PRINTF USING PHP VARAIBLE THE NAME OF THE PERSON AAAAHAHAAH

								carDistances[<?=$x?>][decrementIndex] = tempDist;//update the least index elements distance
								carSeating[<?=$x?>][decrementIndex] = <?php echo json_encode($peopleArray[$y]->name)?>;//update the least index elemetns name
								<?php $peopleArray[$y]->seated = true;?>

								if (decrementIndex < (seatsCount-1))//if the index is less than the last element
								{
									carDistances[<?=$x?>][decrementIndex + 1] = tempSwap;
									carSeating[<?=$x?>][decrementIndex + 1] = tempName;
								}
								decrementIndex--;
							}

							console.log('<?=$y?>' + <?php echo json_encode(sizeOf($peopleArray)- 1)?>);
							if(peopleIterator == (<?php echo json_encode(sizeOf($peopleArray))?>))
							{
								outputLoc.innerHTML += '<br></br>';
								outputLoc.innerHTML += <?php echo json_encode($carsArray[$x]->name)?> + "'s car: ";
								peopleIterator = 0;


								for (var print = 0; print < seatsCount; print++)
								{//print out the people in the car
									outputLoc.innerHTML += carSeating[<?=$x?>][print] + " " + carDistances[<?=$x?>][print] + 'm, ';
								}
							
								console.log(
								<?php echo json_encode($carsArray[$x]->name) ?>
								+ "'s CAR:"
								)

							}
						});

			<?php } ?>//end of people iteration

		<?php } ?> //end of car iteration
		}
	
		</script>
		
	</body>
</html>