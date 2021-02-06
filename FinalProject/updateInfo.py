import json
from urllib.request import Request, urlopen
!pip install mysql-connector
import mysql.connector


def getMyToken():
    return 'pk_13e529c9591d4fa89e36a738dc8af8b1'

def getStock(stock,theType,command):
    url = "https://cloud.iexapis.com/stable/stock/"+stock+"/"+theType+"/"+command+"/?token="+str(getMyToken());
    request = Request(url)
    html = urlopen(request)
    data = html.read()
    data = json.loads(data)
    return data

def connectDB(host,user,passwd,db):
    mydb = mysql.connector.connect(
    host=host,
    user=user,
    passwd=passwd,
    database=db
  )
    return mydb

##Update data -- make cron task to do this either once or twice a day
gme5yChart = getStock('gme','chart','1m')
db = connectDB("imc.kean.edu","sergeach","0991499","2021S_sergeach")
mycursor = db.cursor()

for record in gme5yChart:
    sql = "select count(*) as ct from GMEData where myDate = '"+ str(record['date'] ) +"'"
    mycursor.execute(sql)
    results = mycursor.fetchall()

    for row in results:
        ct = row[0];

        if ct == 0:
            sql = "INSERT INTO GMEData VALUES ('"+str(record['date'])+"', "+str(record['open'])+","+str(record['close'])+","+ str(record['high'])+","+str(record['low'])+","+str(record['volume'])+","+str(record['change'])+","+str(record['changeOverTime'])+")"
            print(sql);
            mycursor.execute(sql)
db.commit()
db.close()
