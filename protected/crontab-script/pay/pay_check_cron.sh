#!/bin/sh

#  pay_check_cron.sh
#  
#
#  Created by 刘团望 on 6/11/14.
#

BASE_PATH="/data/logs/crontab/pay"
if [ ! -d "$BASE_PATH" ];then
    mkdir $BASE_PATH
fi
SCRIPT_PATH="/sp_edaijia/www/v2/protected/crontab-script/pay"

#执行的当天建立一个文件夹
cd $BASE_PATH
day=`date +"%y%m%d"`
if [ ! -d "$day" ];then
    mkdir $day
fi
yesterday=`date -d "-1 day" "+%y%m%d"`

#生成要下单的文件名
downFileName="RD4003"$yesterday"10_898110248990040"
cd $day
if [ ! -f "$downFileName" ];then
    sh $SCRIPT_PATH"/"lftp.sh $downFileName
    #转换一下编码
    iconv -f GBK -t UTF-8 $downFileName  > utf-8.txt
    sed '1,/^_/d' utf-8.txt  > $downFileName
fi
clearDate=`cat utf-8.txt | grep -a "清算日期" | awk '{print $2}' `
#读入统计数据库
/sp_edaijia/www/v2/protected/yiic check ReadRD --fileName=$downFileName --clearDate=$clearDate

#计算生成报表邮件,收到邮件，清算日期是前一天的
currDay=`date +"%Y-%m-%d"`
clearDay=`date -d "-1 day" +"%Y-%m-%d"`
/sp_edaijia/www/v2/protected/yiic check manualCheck --dateStart=$clearDay  --dateEnd=$clearDay


