// Funkcja do pobrania bieżącej daty i wyświetlenia jej w elemencie o id="data"
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
// Funkcja do rozpoczęcia działania zegara
function startclock()
{
    stopclock();
    gettheDate();
    showtime();

}
// Funkcja do wyświetlania aktualnego czasu
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



startclock();  // Automatyczne wywołanie funkcji po załadowaniu skryptu




