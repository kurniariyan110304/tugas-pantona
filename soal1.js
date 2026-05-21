function quickSelect(arr, k) {
    if (arr.length === 1) return arr [0];

    const pivot = arr[Math.floor(arr.length / 2)];

    const left = [];
    const right = [];
    const equal = [];

    for (const num of arr) {
        if (num < pivot){
            left.push(num);
        } else if (num > pivot) {
            right.push(num);
        } else {
            equal.push(num);
        }
    }

    if (k < left.length){
        return quickSelect(left, k);
    }

    if (k < left.length + equal.length){
        return pivot;
    }

    return quickSelect(right, k - left.length - equal.length);
}

    function findMedian(arr) {
        if (!Array.isArray(arr)|| arr.length===0){
            return "Array harus diisi tidak boleh kosong";
        }

        const n  = arr.length;
        const mid = Math.floor(n/2);

        if (n % 2 ===1){
            return quickSelect(arr, mid);
        }

        const leftMiddle = quickSelect(arr, mid - 1);
        const rightMiddle  = quickSelect(arr, mid);

        return (leftMiddle + rightMiddle) / 2;
}

//Test logikanya
console.log (findMedian([7,1,3,5,9]));
console.log(findMedian([7,1,3,5]));
