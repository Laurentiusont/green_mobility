def main():
    # input awal
    awal = int(input('masukan angka :  '))
    inkremen = int(input('inkremen:'))
    n = int(input('pilih operasi : '))
    operasi = input('kali/tambah')
    
    # hitung deret
    if operasi == 'kali':
        for i in range (n,awal,inkremen):
            for j in range(i):
                print(j,operasi)
                
# masih kebalik
main()