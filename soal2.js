function removeDuplicate(arr) {
    const unique = [];
    const removed = [];

    for (let i = 0; i < arr.length; i++){
        if (unique.includes(arr[i])){
            removed.push(arr[i]);
        } else {
            unique.push(arr[i]);
        }
    }

    let totalRemoved  = 0;

    for (let i  = 0; i < removed.length; i++) {
        totalRemoved += removed[i];
    }

    return {
        unique: unique,
        removed: removed,
        totalRemoved: totalRemoved,
    };
}

const dataDuplicate = [1,2,3,2,4,1,5,3];
const resultDuplicate = removeDuplicate(dataDuplicate);

console.log("Array awal:", dataDuplicate);
console.log("Array tanpa ada duplicate:", resultDuplicate.unique);
console.log("Angka yang dihapus:", resultDuplicate.removed);
console.log("Jumlah angka yang sudah dihapus:", removeDuplicate.totalRemoved);