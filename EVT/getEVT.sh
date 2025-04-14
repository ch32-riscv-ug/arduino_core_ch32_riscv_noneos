rm -rfv CH32*

#wget https://www.wch.cn/download/file?id=299 -O CH32F103EVT.ZIP
#wget https://www.wch.cn/download/file?id=364 -O CH32F20xEVT.ZIP

wget --continue https://www.wch.cn/download/file?id=409 -O CH32V003EVT.ZIP
wget --continue https://www.wch.cn/download/file?id=477 -O CH32V006EVT.ZIP
wget --continue https://www.wch.cn/download/file?id=326 -O CH32V103EVT.ZIP
wget --continue https://www.wch.cn/download/file?id=385 -O CH32V20xEVT.ZIP
wget --continue https://www.wch.cn/download/file?id=356 -O CH32V307EVT.ZIP
wget --continue https://www.wch.cn/download/file?id=444 -O CH32X035EVT.ZIP
wget --continue https://www.wch.cn/download/file?id=455 -O CH32L103EVT.ZIP

rm -rfv */

unzip CH32V003EVT.ZIP -d CH32V003
unzip CH32V006EVT.ZIP -d CH32V006
unzip CH32V103EVT.ZIP -d CH32V103
unzip CH32V20xEVT.ZIP -d CH32V20x
unzip CH32V307EVT.ZIP -d CH32V307
unzip CH32X035EVT.ZIP -d CH32X035
unzip CH32L103EVT.ZIP -d CH32L103
