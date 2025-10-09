function fetchAttendance() {
    fetch('../fetch_attendance.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('attendanceBody');
            tbody.innerHTML = ''; // Clear existing rows

            if(data.length === 0){
                tbody.innerHTML = "<tr><td colspan='4' class='text-center text-muted'>No assigned attendance records found.</td></tr>";
                return;
            }

            data.forEach(row => {
                const nameParts = [row.last_name, row.first_name, row.middle_name, row.suffix].filter(Boolean).join(' ');
                const fullName = nameParts.replace(/^(.+)$/, row.last_name + ", " + row.first_name + (row.middle_name ? " " + row.middle_name : "") + (row.suffix ? " " + row.suffix : ""));
                const scanTime = new Date(row.scan_time);
                const formattedTime = scanTime.toLocaleString('en-US', { month:'long', day:'numeric', hour:'numeric', minute:'2-digit', hour12:true });

                tbody.innerHTML += `
                    <tr>
                        <td>${row.card_uid}</td>
                        <td>${fullName}</td>
                        <td>${formattedTime}</td>
                    </tr>
                `;
            });
        })
        .catch(error => console.error('Error fetching attendance:', error));
}

// Initial fetch
fetchAttendance();

// Auto-refresh every 5 seconds
setInterval(fetchAttendance, 5000);