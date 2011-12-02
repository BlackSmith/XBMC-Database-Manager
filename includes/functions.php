<?php
		function dbopen()
		{
			global $dbconn, $hostname, $database, $username, $password;
			try 
			{
				$dbconn = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
			}
			catch(PDOException $e)
			{
				printf($e->getMessage());
			}
		}
		function dbclose()
		{
			global $dbconn;
			$dbconn = null;
		}
		
		function dbquery($query)
		{
			global $dbconn;
			$result = $dbconn->query($query);
			return $result;
		}
		
		function getPath($id)
		{
			switch ($_GET["view"])
			{
				case "movies":
					$q = "SELECT idFile FROM movie WHERE idMovie = " . $id;
					foreach (dbquery($q) as $temp)
					{
						$idfile = $temp['idFile'];
					}
					$q = "SELECT idPath,strFilename FROM files WHERE idFile = " . $idfile;
					foreach (dbquery($q) as $temp)
					{
						$filename = $temp['strFilename'];
						$idpath = $temp['idPath'];
					}
					break;
				case "shows":
					$q = "SELECT idPath FROM tvshowlinkpath WHERE idShow = " . $id;
					foreach (dbquery($q) as $temp)
					{
						$idpath = $temp['idPath'];
					}
					break;
			}
			$q = "SELECT strPath FROM path WHERE idPath = ". $idpath;
			foreach (dbquery($q) as $temp)
			{
				$strpath = $temp['strPath'];
			}
			return $strpath;
		}
		
		function MovieFile($id)
		{
			$q = "SELECT idFile FROM movie WHERE idMovie = " . $id;
			foreach (dbquery($q) as $temp)
			{
					$idfile = $temp['idFile'];
			}
			$q = "SELECT strFilename FROM files WHERE idFile = " . $idfile;
			foreach (dbquery($q) as $temp)
			{
					$filename = $temp['strFilename'];
			}
			return $filename;
		}
		
		function numEpisodes($id)
		{
			global $dbconn;
			$numtest = $dbconn->prepare("SELECT idEpisode FROM tvshowlinkepisode WHERE idShow = " . $id);
			$numtest->execute();
			$count = $numtest->rowCount();
			#foreach (dbquery($q) as $temp)
			#{
			#		$filename = $temp['strFilename'];
			#}
			return $count;
		}
		
		function PrintInfo($id)
		{
			switch ($_GET["view"])
			{
				case "movies":
					$q = "SELECT c00,c15,c06,c18,c14,c07,c11,c05,c03,c02,idFile,c09,c01 FROM movie WHERE idMovie = " . $id;
					$col1 = array("Director","Writer", "Studio", "Genre", "Year", "Runtime", "Rating", "Tagline", "Plot Outline", "File", "External Info", "Plot");
					$temp = dbquery($q);
					while ($row = $temp->fetch (PDO::FETCH_NUM))
						$col2 = $row;										#Sets last row from query
					$col2[7] = number_format($col2[7],1);					#Format rating to 1 decimal
					$col2[10] = getPath($id) . MovieFile($id);
					break;
					
				case "shows":
					$q = "SELECT c00,c02,c05,c08,c04,c14,idShow,c01 FROM tvshow WHERE idShow = " . $id;
					$col1 = array("Episodes","First Aired", "Genre", "Rating", "Network", "Path", "Plot");
					$temp = dbquery($q);
					while ($row = $temp->fetch (PDO::FETCH_NUM))
						$col2 = $row;										#Sets last row from query
					$col2[1] = numEpisodes($id);
					$col2[4] = number_format($col2[4],1);					#Format rating to 1 decimal
					$col2[6] = getPath($id);
					break;
			}
			?>
			<table border="0">
				<tr>
					<td>
						<?php echo "<h1>" . $title . "</h1>"; ?>
					</td>
				</tr>
			</table>
			<table border="1">
				<?php
					$i = "1";
					foreach ($col1 as $temp)
					{
						echo "<tr><th><p>" . $temp . "</p></th><td>" . $col2[$i] . "</td></tr>";
						$i++;
					}
				?>
			</table>
			<?php
		}
?>