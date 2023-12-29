import moment from "moment";

function padNumber(number)
{
    return (number > 9 || number === 0) ? number : "0" + number
}

function readableTimeFromSeconds(seconds)
{
    const hours = 3600 > seconds ? 0 : parseInt(seconds / 3600, 10)

    return {
        hours: padNumber(hours),
        seconds: padNumber(seconds % 60),
        minutes: padNumber(parseInt(seconds / 60, 10) % 60),
    }
}

function secondsReadable(seconds) {
    const time = readableTimeFromSeconds(seconds);

    return `${time.hours} Hours | ${time.minutes} mins | ${time.seconds} seconds`
}

function getSecondsGoneToNow (startedAtMilliseconds) {
    const started = moment(startedAtMilliseconds);
    const stopped = moment(moment.now());

    return Math.round(stopped.diff(started) / 1000)
}


export {secondsReadable, getSecondsGoneToNow}
