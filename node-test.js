// 1. Mencari 3 angka dari array yang jika dijumlahkan = 0

function cariTigaAngkaJumlahNol(arrayNumber) {
    for (let i = 0; i < arrayNumber.length - 2; i++) {
      for (let j = i + 1; j < arrayNumber.length - 1; j++) {
        for (let k = j + 1; k < arrayNumber.length; k++) {
          if (arrayNumber[i] + arrayNumber[j] + arrayNumber[k] === 0) {
            return [arrayNumber[i], arrayNumber[j], arrayNumber[k]];
          }
        }
      }
    }
  
    return "Not Found";
  }
  
  
  // 2. Menghilangkan angka yang sama dari array tanpa menggunakan fungsi array_unique
  
  function hilangkanAngkaSama(arrayNumber) {
    let hasil = [];
  
    for (let i = 0; i < arrayNumber.length; i++) {
      let sudahAda = false;
  
      for (let j = 0; j < hasil.length; j++) {
        if (arrayNumber[i] === hasil[j]) {
          sudahAda = true;
          break;
        }
      }
  
      if (!sudahAda) {
        hasil.push(arrayNumber[i]);
      }
    }
  
    return hasil;
  }
  
  
  // 3. Sorting angka dari array Tanpa menggunakan fungsi array_sort
  
  function sortingCustom(arrayNumber) {
    let arrayCopy = [...arrayNumber];
  
    // Sorting manual ascending menggunakan bubble sort
    for (let i = 0; i < arrayCopy.length - 1; i++) {
      for (let j = 0; j < arrayCopy.length - 1 - i; j++) {
        if (arrayCopy[j] > arrayCopy[j + 1]) {
          let temp = arrayCopy[j];
          arrayCopy[j] = arrayCopy[j + 1];
          arrayCopy[j + 1] = temp;
        }
      }
    }
  
    let hasil = [];
    let groupKe = 0;
  
    for (let i = 0; i < arrayCopy.length; i += 5) {
      let group = [];
  
      for (let j = i; j < i + 5 && j < arrayCopy.length; j++) {
        group.push(arrayCopy[j]);
      }
  
      // Group genap: ascending
      if (groupKe % 2 === 0) {
        for (let j = 0; j < group.length; j++) {
          hasil.push(group[j]);
        }
      } 
      // Group ganjil: descending
      else {
        for (let j = group.length - 1; j >= 0; j--) {
          hasil.push(group[j]);
        }
      }
  
      groupKe++;
    }
  
    return hasil;
  }
  
    // 4. Mengecek string simetris / palindrome tanpa menggunakan fungsi strrev
  
  function cekStringSimetris(str) {
    let kiri = 0;
    let kanan = str.length - 1;
  
    while (kiri < kanan) {
      if (str[kiri] !== str[kanan]) {
        return false;
      }
  
      kiri++;
      kanan--;
    }
  
    return true;
  }
  
  
  // ======================================================
  // CONTOH PENGGUNAAN
  // ======================================================
  
  console.log("===== SOAL 1 =====");
  let array1 = [2, 1, 5, 7, 4, -8, -3, -1];
  console.log("Input :", array1);
  console.log("Output:", cariTigaAngkaJumlahNol(array1).toString());
  
  
  console.log("\n===== SOAL 2 =====");
  let array2 = [1, 1, 4, 4, 4, 5, 5, 6, 8, 9, 10, 10, 12, 13, 13, 17];
  console.log("Input :", array2);
  console.log("Output:", hilangkanAngkaSama(array2).toString());
  
  
  console.log("\n===== SOAL 3 =====");
  let array3 = [2, 5, 1, 12, -5, 4, -1, 3, -3, 20, 8, 7, -2, 6, 9];
  console.log("Input :", array3);
  console.log("Output:", sortingCustom(array3).toString());
  
  
  console.log("\n===== SOAL 4 =====");
  let str1 = "madam";
  let str2 = "gozaru";
  
  console.log("Input :", str1);
  console.log("Output:", cekStringSimetris(str1) ? "TRUE" : "FALSE");
  
  console.log("Input :", str2);
  console.log("Output:", cekStringSimetris(str2) ? "TRUE" : "FALSE");