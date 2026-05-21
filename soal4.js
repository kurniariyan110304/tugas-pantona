function customSort(arr) {
    const result = [...arr];

    for(let i = 0; i < result.length - 1; i++) {
        for (let j = 0; j <result.length - i - 1; j++){
            if (result[j] > result[j+1]){
                const temp = result[j];
                result[j] = result[j+1];
                result[j+1] = temp;
            }
        }
    }

    return result;
}

const unsortedArray = [5,3,8,1,2];
const sortedArray = customSort(unsortedArray);

console.log(" Array awal:", unsortedArray);
console.log("Hasil custom dari sort:", sortedArray);
console.log("Algoritma yang digunakan adalah: Bubble sort");