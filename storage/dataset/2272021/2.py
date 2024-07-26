# File : 2.py 
# Penulis : Christofer Eric
# Tujuan Program : membuat program menghitung increment suku dan operasi
# Kamus Data

def main():
    # integer suku
    s = int(input("Jumlah Suku : "))
    # integer awal
    awl = int(input("Awal : "))
    # integer increment
    inc = int(input("Increment : "))
    # integer operasi
    opr = str(input("Operasi : "))
    tot = 0
    # output
    if (opr == "+"):
        # loop i
        for i in range (s):
            num = awl
            tot = tot + num
            awl = awl + inc
            print(num,end="")
            if(i<s-1):
                print("+",end="")
            else:
                print()
    elif (opr == "-"):
        for i in range (s):
            num = awl
            tot = tot - num
            awl = awl + inc
            print(num,end="")
            if(i<s-1):
                print("-",end="")
            else:
                print()
    elif (opr == "x"):
        tot = tot + 1
        for i in range (s):
            num = awl
            tot = tot * num
            awl = awl + inc
            print(num,end="")
            if(i<s-1):
                print("x",end="")
            else:

                print()
    elif (opr == ":"):
        tot = (tot + 1)*(awl*2)
        for i in range (s):
            num = awl
            tot = tot / num
            awl = awl + inc
            print(num,end="")
            if(i<s-1):
                print(":",end="")
            else:
                print()
    print("Jumlah Deret : ",tot)
            
    return 0

    
if __name__ == '__main__':    
    main()   