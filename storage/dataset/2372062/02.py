def main():
    #int n
    n = int(input("Masukkan jumlah suku: "))
    #int awal
    awal = int(input("Nilai awal: "))
    #int increment
    increment = int(input("Masukkan increment: "))
    #string operasi
    operasi = str(input("Masukkan Operasi(+, -, *): "))
    
    if operasi == "+":
        for i in range (n, awal, increment):
            for j in range(i):
                print(j, operasi)

    if operasi == "-":
        for i in range (n, awal, increment):
            for j in range(i):
                print(j, operasi)

    if operasi == "*":
        for i in range (n, awal, increment):
            for j in range(i):
                print(j, operasi)
main()