def main():
    suku = int(input("Jumlah Suku: "))
    awal = int(input("Awal: "))
    incre = int(input("Increment: "))
    op = input("Operasi: ")
    result = awal 
    pesan_deret = "Deret: \n" + str(awal)
    
    for i in range(suku - 1):
        if(op == "+"):
            awal += incre
            pesan_deret += op + str(awal)  
            result += awal
        
        elif(op == "-"):
            awal += incre
            pesan_deret += op + str(awal) 
            result -= awal
        
        elif(op == "*"):
            awal += incre
            pesan_deret += op + str(awal) 
            result *= awal 
            
        else:
            awal += incre
            pesan_deret += op + str(awal) 
            result /= awal 

    print(pesan_deret)
    print("Jumlah Deret:", result)
main()    