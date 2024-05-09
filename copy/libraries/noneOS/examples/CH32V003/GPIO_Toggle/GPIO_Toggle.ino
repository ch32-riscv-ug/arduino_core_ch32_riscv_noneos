void GPIO_Toggle_INIT(void) {
    GPIO_InitTypeDef GPIO_InitStructure = {0};

    RCC_APB2PeriphClockCmd(RCC_APB2Periph_GPIOD, ENABLE);
    GPIO_InitStructure.GPIO_Pin = GPIO_Pin_0;
    GPIO_InitStructure.GPIO_Mode = GPIO_Mode_Out_PP;
    GPIO_InitStructure.GPIO_Speed = GPIO_Speed_50MHz;
    GPIO_Init(GPIOD, &GPIO_InitStructure);
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
