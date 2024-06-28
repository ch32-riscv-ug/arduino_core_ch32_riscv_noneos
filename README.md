# arduino_core_ch32_riscv_noneos
CH32 Risc-V noneOS for Arduino IDE (not ArduinoAPI)

## Board manager URL

```
https://raw.githubusercontent.com/ch32-riscv-ug/arduino_core_ch32_riscv_noneos/main/package_ch32-riscv-noneos.json
```

## Overview

This board manager is for building CH32's RISC-V series with Arduino IDE.

- https://github.com/openwch/arduino_core_ch32

It is based on openwch's Arduino Core.

However, it does not include the Arduino Core API. pinMode() and digitalWrite() do not work.
The EVT sample code has been adjusted to work.

- https://github.com/ch32-riscv-ug/arduino_core_ch32_riscv_arduino

We are also working on creating one that is compatible with the Arduino Core API.

## sample code
```c
void GPIO_Toggle_INIT(void) {
  GPIO_InitTypeDef GPIO_InitStructure = { 0 };

  RCC_APB2PeriphClockCmd(RCC_APB2Periph_GPIOA, ENABLE);
  GPIO_InitStructure.GPIO_Pin = GPIO_Pin_0;
  GPIO_InitStructure.GPIO_Mode = GPIO_Mode_Out_PP;
  GPIO_InitStructure.GPIO_Speed = GPIO_Speed_50MHz;
  GPIO_Init(GPIOA, &GPIO_InitStructure);
}

void setup() {
  NVIC_PriorityGroupConfig(NVIC_PriorityGroup_1);
  SystemCoreClockUpdate();
  Delay_Init();
  USART_Printf_Init(115200);
  printf("SystemClk:%d\r\n", SystemCoreClock);
  printf("ChipID:%08x\r\n", DBGMCU_GetCHIPID());
  printf("GPIO Toggle TEST\r\n");
  GPIO_Toggle_INIT();
}

void loop() {
  static u8 i = 0;
  Delay_Ms(250);
  GPIO_WriteBit(GPIOA, GPIO_Pin_0, (i == 0) ? (i = Bit_SET) : (i = Bit_RESET));
  printf("GPIO_WriteBit %d\n", i);
}
```

There are setup() and loop() functions, but the EVT code will work as is.

## Structure

This repository contains tools and files to create board managers. The generated files are in the release.

### EVT folder

This is the folder where the EVT download script and downloaded files are saved.

```
bash getEVT.sh
```

This command will download and extract it.

### copy folder

This is a file that is copied and used. This includes patches for EVT files and other necessary files.

### common folder

This is a common file used for each chip environment.
It is used by copying each chip.

## Build method

```
php update.php
```

### Step1. EVT and common folder copy

```
/cores/CH32L10x/SRC
/cores/CH32L10x/USER
/cores/CH32V00x/SRC
/cores/CH32V00x/USER
...
/cores/CH32X03x/SRC
/cores/CH32X03x/USER
```

Under cores is usually the arduino. Since the CH32 series has a different environment depending on the chip, separate folders for each series and copy the EVT.

```
cp -rfv $path/EVT/EXAM/SRC/* $dir/SRC
```

Copy all SRC folders.

```
rename .S .inc $dir/SRC/Startup/*.S
```

However, if there is more than one S file extension for the assembler, an error will occur, so change it to inc.
Later, read one inc file in the S file in the copy folder.

```
cp -rfv $path/EVT/EXAM/GPIO/GPIO_Toggle/User/* $dir/USER
```

The USER folder uses GPIO_Toggle.

```
rm -rfv $dir/USER/main.c
```

Delete main.c.

```
cp -rfv ../../common/* $dir/
```

There is a new main.c in the common folder.

```c
#include "Arduino.h"

void c_main( void ) __attribute__(( weak ));
void c_main( void )
{
}

int main(void) __attribute__(( weak ));
{
    c_main();

    setup();

    for (;;)
    {
        loop();
    }
}
```

The new main.c is above. It calls Arduino's setup() and loop() functions. However, since Arduino functions are C++, we also have a c_main() function for C language.
An example sketch for the C language is available in common/c_main.

```c
#include "debug.h"

#ifdef __cplusplus
extern "C" {
#endif

void setup();
void loop();

#ifdef __cplusplus
}
#endif
```

The contents of Arduino.h are also simple.

### Step2. copy folder copy

```
cp -rfv ../copy/* .
```

Copy the contents of the copy folder.

### Step3. patch

```
patch < /cores/CH32V10x/SRC/Peripheral/inc/ch32v10x_misc.h.patch
```

Search for files with the patch extension and run the patch.

There is a bug in ch32v10x_misc.h of CH32V10x that the closing parenthesis of "extern "C" {" is missing.
EVT samples are all in C, so they are not affected by this.

### Step4. zip

Create a ZIP file for release.
