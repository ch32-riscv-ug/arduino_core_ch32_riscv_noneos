#include "debug.h"

void c_main() {
  u8 i = 0;

  SystemCoreClockUpdate();
  Delay_Init();
  USART_Printf_Init(115200);
  printf("SystemClk:%d\r\n", SystemCoreClock);
  printf("ChipID:%08x\r\n", DBGMCU_GetCHIPID());
  printf("C main TEST\r\n");

  while (1) {
    Delay_Ms(250);
    i++;
    printf("i = %d\r\n", i);
  }
}
