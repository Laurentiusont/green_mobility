def main():
    suku = int(input("Jumlah Suku: "))
    awal = int(input("Awal: "))
    jarak = int(input("increment: "))
    operasi =input("operasi :")
    n = 0
    for i in range(awal,suku*jarak,jarak):
        if (operasi=="+"):
            n = n + i
            print(i,end="+")
        elif (operasi=="*"):     
            n = (n+1) * i
            print(i,end="*")
        elif (operasi=="-"):
            n = n - i
            print(i,end="-")
        suku = suku + 1
            
    print (f"jumlah deret  = {n}")
        
main()