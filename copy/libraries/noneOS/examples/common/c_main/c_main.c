#include "debug.h"

void c_main() {
  uint8_t i = 0;

  SystemCoreClockUpdate();
  Delay_Init();
  USART_Printf_Init(115200);
  printf("SystemClk:%u\r\n", (unsigned int)SystemCoreClock);
  printf("ChipID:%08x\r\n", (unsigned int)DBGMCU_GetCHIPID());
  printf("C main TEST\r\n");

  while (1) {
    Delay_Ms(250);
    i++;
    printf("i = %d\r\n", i);
  }
}
