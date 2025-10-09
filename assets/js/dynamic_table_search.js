document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("userSearch");
    if (!searchInput) return; // safety check

    searchInput.addEventListener("keyup", function() {
        let filter = this.value.toLowerCase();
        let table = document.getElementById("attendanceTable");
        let trs = table.getElementsByTagName("tr");

        for (let i = 1; i < trs.length; i++) { // skip header
            let tds = trs[i].getElementsByTagName("td");
            let show = false;

            for (let j = 0; j < tds.length - 1; j++) { // exclude Action column
                if (tds[j].textContent.toLowerCase().indexOf(filter) > -1) {
                    show = true;
                    break;
                }
            }

            trs[i].style.display = show ? "" : "none";
        }
    });
});
