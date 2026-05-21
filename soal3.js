function isSymmetric(str){
    let reserved = "";

    for (let i = str.length - 1; i >=0; i--){
        reserved += str[i];
    }

    return str === reserved;
}

function makeSymmetric(str) {
    if(isSymmetric(str)) {
        return str;
    }

    let reservedWithouthLastChar = "";
    for (let i = str.length - 2; i >= 0; i--){
        reservedWithouthLastChar += str[i];
    }

    return str + reservedWithouthLastChar;
}

const word1 = "Gajah";
const word2 = "Guru";

console.log("Kata:", word1);
console.log("Apakah symmetric?", isSymmetric(word1));
console.log("Hasil symmetric:", makeSymmetric(word1));

console.log("Kata:", word2);
console.log("Apakah symmetric?", isSymmetric(word2));
console.log("Hasil symmetric:", makeSymmetric(word2));