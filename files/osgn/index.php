<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Giochi OSGN</title>
   <link rel="icon" href="icon.png" type="image/png">
   <style>
       body {
           font-family: Arial, sans-serif;
           background-color: #f4f4f4;
           margin: 0;
           padding: 0;
           display: flex;
           min-height: 100vh;
       }

       #dataTable th:nth-child(1),
       #dataTable td:nth-child(1) {
           width: 30%;
       }

       #dataTable th:nth-child(2),
       #dataTable td:nth-child(2) {
           width: 20%;
       }

       #dataTable th:nth-child(3),
       #dataTable td:nth-child(3) {
           width: 40%;
       }

       .sidebar {
           width: 250px;
           background-color: #333;
           color: #fff;
           padding: 20px;
           box-sizing: border-box;
           height: 10000vh;
           overflow-y: auto;
           transition: width 0.3s ease;
           position: relative;
       }

       .sidebar.collapsed {
           width: 60px;
       }

       .sidebar.collapsed .tag-list {
           display: none;
       }

       .sidebar-toggle {
           position: absolute;
           top: 10px;
           right: 10px;
           cursor: pointer;
           font-size: 20px;
           background: none;
           border: none;
           color: #fff;
           padding: 5px;
           transition: transform 0.3s ease;
       }

       .sidebar-toggle:focus {
           outline: none;
       }

       .content {
           flex-grow: 1;
           padding: 20px;
       }

       h2 {
           color: #333;
           margin-bottom: 20px;
       }

       table {
           width: 100%;
           border-collapse: collapse;
           border-radius: 8px;
           overflow: hidden;
           margin-bottom: 20px;
       }

       th, td {
           padding: 12px 15px;
           text-align: left;
           border-bottom: 1px solid #ddd;
       }

       th {
           background-color: #f2f2f2;
       }

       tr:hover {
           background-color: #f9f9f9;
       }

       td {
           word-break: break-word;
       }

       .error {
           color: red;
       }

       .search-container {
           margin-bottom: 20px;
       }

       .search-container input[type=text] {
           padding: 10px;
           width: 100%;
           border-radius: 5px;
           border: 1px solid #ccc;
           box-sizing: border-box;
           font-size: 16px;
       }

       .tag-list {
           list-style: none;
           padding: 0;
       }

       .tag-list li {
           margin-bottom: 5px;
       }

       .tag-list li a {
           color: #fff;
           text-decoration: none;
           display: block;
           padding: 5px 10px;
           background-color: #555;
           border-radius: 5px;
           transition: background-color 0.3s ease;
       }

       .tag-list li a:hover {
           background-color: #777;
       }

       .table-link a {
           text-decoration: none;
           color: inherit;
       }

       .table-link a:hover {
           color: inherit;
       }
   </style>
</head>
<body>

<div class="sidebar">
   <h2>Tags</h2>
   <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar" aria-expanded="true">&#9776;</button>
   <ul class="tag-list">
       <?php
       $servername = "tmhome.tplinkdns.com";
       $username = "dbview";
       $password = "viewpsw";
       $dbname = "tdbosgn";
       $port = "11506";

       $conn = new mysqli($servername, $username, $password, $dbname, $port);

       if ($conn->connect_error) {
           die("<li><a href='#' class='error'>Connection failed: " . $conn->connect_error . "</a></li>");
       }

       $sql = "SELECT tag FROM games";
       $result = $conn->query($sql);

       $tags = [];
       if ($result->num_rows > 0) {
           while ($row = $result->fetch_assoc()) {
               $tagArr = explode(',', $row['tag']);
               foreach ($tagArr as $tag) {
                   $tag = trim($tag);
                   if (!in_array($tag, $tags)) {
                       $tags[] = $tag;
                       echo "<li><a href='#' onclick='filterTable(\"$tag\")'>" . $tag . "</a></li>";
                   }
               }
           }
       } else {
           echo "<li><a href='#'>No tags found</a></li>";
       }

       $conn->close();
       ?>
   </ul>
</div>

<div class="content">
   <h2>Giochi</h2>

   <div class="search-container">
       <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Cerca per Nome...">
   </div>

   <table id="dataTable">
       <thead>
           <tr>
               <th>Name</th>
               <th>ID</th>
               <th>Tag</th>
           </tr>
       </thead>
       <tbody>
           <?php
           $conn = new mysqli($servername, $username, $password, $dbname, $port);

           if ($conn->connect_error) {
               die("<tr><td colspan='3' class='error'>Connection failed: " . $conn->connect_error . "</td></tr>");
           }

           $sql = "SELECT name, id, tag FROM games";
           $result = $conn->query($sql);

           if ($result->num_rows > 0) {
               while ($row = $result->fetch_assoc()) {
                   echo "<tr class='data-row'>";
                   echo "<td class='table-link'><a href='dettaglio_gioco.php?id=" . $row["id"] . "'>" . $row["name"] . "</a></td>";
                   echo "<td>" . $row["id"] . "</td>";
                   echo "<td>" . $row["tag"] . "</td>";
                   echo "</tr>";
               }
           } else {
               echo "<tr><td colspan='3'>Nessun risultato trovato</td></tr>";
           }

           $conn->close();
           ?>
       </tbody>
   </table>
</div>

<script>
   function searchTable() {
       const input = document.getElementById("searchInput");
       const filter = input.value.toUpperCase();
       const table = document.getElementById("dataTable");
       const tr = table.getElementsByClassName("data-row");

       for (let i = 0; i < tr.length; i++) {
           const td = tr[i].getElementsByTagName("td")[0];
           if (td) {
               const txtValue = td.textContent || td.innerText;
               tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
           }
       }
   }

   function filterTable(tag) {
       const table = document.getElementById("dataTable");
       const tr = table.getElementsByClassName("data-row");

       for (let i = 0; i < tr.length; i++) {
           const td = tr[i].getElementsByTagName("td")[2];
           if (td) {
               tr[i].style.display = td.innerHTML.includes(tag) ? "" : "none";
           }
       }
   }

   function toggleSidebar() {
       const sidebar = document.querySelector(".sidebar");
       sidebar.classList.toggle("collapsed");
       const expanded = !sidebar.classList.contains("collapsed");
       const toggleButton = document.querySelector(".sidebar-toggle");
       toggleButton.setAttribute("aria-expanded", expanded);
   }
</script>
</body>
</html>
