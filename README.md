# MsgLog
<p align =  "center">
<b>MsgLog -</b> это самый простой плагин, который позволяет просматривать сообщения любого игрока в удобное для вас время.<br>
<br>
<a href="https://github.com/CL4M1N3/ChatLog"><img src="https://github.com/cl4m1n3/MsgLog/blob/RedCoreMCPE/decorations/icon.png"></img></a><br>
<b>Запись сообщений, отправленных игроком в чат.</b>
</p>

# Что умеет MsgLog?

<ul>
<li>Бесконечная запись сообщений каждого игрока</li>
<li>Запись дополнительных данных о сообщении</li>
<li>Простейшее отображение всех сообщений в формах</li>
<li>Возможность использовать функции MsgLog в других плагинах</li>
</ul>

# Как использовать?

> **Поскольку плагин еще не реализовал свои собственные формы, необходимо использовать сторонний плагин FormAPI!**

Чтобы открыть настройки плагина, введите <b>/msglog</b>:
<p align =  "center">
<a href="https://github.com/CL4M1N3/MsgLog"><img src="https://github.com/cl4m1n3/MsgLog/blob/RedCoreMCPE/decorations/img_1.jpg"></img></a><br>
</p>
<br>
<br>
Чтобы получить список всех отправленных сообщений игрока, введите <b>/msglog [ник игрока]</b>:
<p align =  "center">
<a href="https://github.com/CL4M1N3/MsgLog"><img src="https://github.com/cl4m1n3/MsgLog/blob/RedCoreMCPE/decorations/img_2.jpg"></img></a><br>
</p>
<br>
<br>
Если вы хотите получить подробную информацию о сообщении, введите <b>/msglog [ник игрока] [ID сообщения]</b>:
<p align =  "center">
<a href="https://github.com/CL4M1N3/MsgLog"><img src="https://github.com/cl4m1n3/MsgLog/blob/RedCoreMCPE/decorations/img_3.jpg"></img></a><br>
</p>

# Как использовать функции MsgLog в других плагинах?

Чтобы записать сообщение, плагин имеет функцию recordMessage(); 
```
public function recordMessage(string $nick, $msg) : void
```
Для того, чтобы вставить ее в свой плагин, воспользуйтесь простым кодом:
```
//$message = "сообщение";
$plugin = $this->getServer()->getPluginManager()->getPlugin("MsgLog");
$plugin->recordMessage($player->getName(), $message);
```
