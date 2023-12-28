
function read(key){
    const val = localStorage.getItem(key)

    if (!val || val === 'undefined') {
        return null;
    }

    return JSON.parse(val);
}

function writeIfDoesntExist(key, data){
    if (read(key)) {
        return false
    }

    write(key, data)

    return true
}

function remove(key) {
    localStorage.removeItem(key)
}

function write(key, data) {
    console.log(data)

    if (data === null || data === '' || data === 'undefined') {
        localStorage.removeItem(key);
    } else {
        localStorage.setItem(key, JSON.stringify(data));
    }
}

export {read, write, writeIfDoesntExist, remove}
