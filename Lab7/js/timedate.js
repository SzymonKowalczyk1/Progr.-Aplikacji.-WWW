
function gettheDate()
{
    Todays = new Date();
    TheDate ="" + (Todays.getMonth()+1) + " / " + Todays.getDate() + " / " +(Todays.getYear()-100);
    document.getElementById("data").innerHTML = TheDate;

}
var timerid = null;
var timerrunning = false;

function stopclock()
{
    if(timerrunning)
        clearTimeout(timerid);
    timerrunning = false;
}

function startclock()
{
    stopclock();
    gettheDate();
    showtime();

}

function showtime()
{
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var seconds = now.getSeconds();
    var timevalue = "" + ((hours > 12) ? hours -12 :hours)
    timevalue += ((minutes < 10) ? ":0" : ":") + minutes
    timevalue += ((seconds < 10) ? ":0" : ":") + seconds
    timevalue += (hours >=12) ? "P.M." : "A.M."
    document.getElementById("zegarek").innerHTML = timevalue;
    timerid = setTimeout("showtime()",1000);
    timerrunning = true;

}
console.log("Plik timedate.js został załadowany");

// ... Twój kod funkcji gettheDate, stopclock, showtime, itd.

startclock();  // Automatyczne wywołanie funkcji po załadowaniu skryptu




