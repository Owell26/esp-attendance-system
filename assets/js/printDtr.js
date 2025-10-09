function printDTR() {
    var content = document.getElementById("dtrTable").outerHTML;

    // Kunin yung values mula sa filter form
    var month = document.getElementById("month").options[document.getElementById("month").selectedIndex].text;
    var year = new Date().getFullYear();
    var userSelect = document.getElementById("user_id");
    var user = userSelect.options[userSelect.selectedIndex].text;
    var userType = userSelect.options[userSelect.selectedIndex].getAttribute("data-type"); // ✅
    var dayRange = document.getElementById("day_range").options[document.getElementById("day_range").selectedIndex].text;

    var mywindow = window.open('', '', 'width=900,height=600');
    mywindow.document.write(`
        <html>
            <head>
                <title>Ctech Attendance System</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .header { text-align: center; margin-bottom: 20px; }
                    .header h2, .header h3, .header p { margin: 5px 0; }

                    #dtrTable {
                        display: flex;
                        gap: 20px;
                        justify-content: center;
                    }
                    table { 
                        border-collapse: collapse; 
                        margin-top: 5px; 
                        width: auto; 
                    }
                    th, td { 
                        border: 1px solid black; 
                        padding: 5px; 
                        text-align: center; 
                    }
                    .content table th,
                    .content table td {
                        font-size: 13px;   /* mas maliit pa sa loob ng table */
                    }

                </style>
            </head>
            <body contenteditable="true">
                <div class="header">
                    <h2 style='margin-bottom:20px;'>Ctech Daily Time Record</h2>
                    <p style='text-align:left;margin-bottom: 15px;'><strong>Name:</strong> ${user}</p>
                    <p style='text-align:left;margin-bottom: 15px;'><strong>User Type:</strong> ${userType}</p>
                    <p style='text-align:left;margin-bottom: 15px;'><strong>Days:</strong> ${dayRange}</p>
                </div>
                <div class="content">
                    ${content}
                </div>

                <div class="signatures" style="margin-top:30px; font-size:14px;">
                    <div style="display:flex; justify-content:space-between; margin:0 50px;">
                        <div style="text-align:center;">
                            <p>__________________________</p>
                            <p>Signature</p>
                        </div>
                        <div style="text-align:center;">
                            <p>__________________________</p>
                            <p>Signature</p>
                        </div>
                    </div>
                </div>

                <div class="signatures" style="margin-top:10px; font-size:14px;">
                    <div style="display:flex; justify-content:space-between; margin:0 50px;">
                        <div style="text-align:center;">
                            <p>__________________________</p>
                            <p>Supervisor's Signature</p>
                        </div>
                        <div style="text-align:center;">
                            <p>__________________________</p>
                            <p>Supervisor's Signature</p>
                        </div>
                    </div>
                </div>
            </body>
        </html>
    `);
    mywindow.document.close();
    mywindow.focus();
    mywindow.print();
}
