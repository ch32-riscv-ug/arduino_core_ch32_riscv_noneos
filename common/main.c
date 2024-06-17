#include "Arduino.h"

void c_main( void ) __attribute__(( weak ));
void c_main( void )
{
}

int main(void) __attribute__(( weak ));
int main(void)
{
    c_main();

    setup();

    for (;;)
    {
        loop();
    }
}

void _init() {}
void _fini() {}
