import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);
  app.setGlobalPrefix('api/v1/bookings');
  const port = process.env.SERVICE_PORT || 3002;
  await app.listen(port, '0.0.0.0');
}
bootstrap();
