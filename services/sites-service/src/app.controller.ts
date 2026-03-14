import { Controller, Get } from '@nestjs/common';

@Controller()
export class AppController {
  @Get('health')
  health() {
    return { status: 'ok', service: 'sites' };
  }

  @Get()
  index() {
    return { data: [], message: 'Sites service placeholder' };
  }
}
