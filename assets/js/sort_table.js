function sortTable(n) {
    let table = document.getElementById("attendanceTable");
    let switching = true;
    let dir = "asc";
    let switchcount = 0;

    while (switching) {
        switching = false;
        let rows = table.rows;

        for (let i = 1; i < rows.length - 1; i++) { // skip header
            let shouldSwitch = false;
            let x = rows[i].getElementsByTagName("TD")[n];
            let y = rows[i + 1].getElementsByTagName("TD")[n];

            let xContent = x.textContent.toLowerCase();
            let yContent = y.textContent.toLowerCase();

            if (dir === "asc") {
                if (xContent > yContent) {
                    shouldSwitch = true;
                }
            } else if (dir === "desc") {
                if (xContent < yContent) {
                    shouldSwitch = true;
                }
            }

            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                switchcount++;
            }
        }

        if (switchcount === 0 && dir === "asc") {
            dir = "desc";
            switching = true;
        }
    }
}