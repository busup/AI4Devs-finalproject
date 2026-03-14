import { Controller, Get } from '@nestjs/common';

@Controller()
export class AppController {
  @Get('health')
  health() {
    return { status: 'ok', service: 'booking' };
  }

  @Get()
  index() {
    return { data: [], message: 'Booking service placeholder' };
  }
}
