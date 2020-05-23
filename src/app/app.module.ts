import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpClientModule, HTTP_INTERCEPTORS }    from '@angular/common/http';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { LoginComponent } from './login/login.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { RouterModule } from '@angular/router';
import { ReactiveFormsModule, FormsModule} from '@angular/forms';
import { HomeComponent } from './home/home.component';
import { EventsComponent } from './events/events.component';
import { ActivateGuard } from './activate.guard';
import { ApiService } from './api.service';
import { EventshowComponent } from './eventshow/eventshow.component';
import { NewuserloginComponent } from './newuserlogin/newuserlogin.component';
// import { Approutes } from './Routing';
import { Interceptor } from './interceptor';
import {Active2Guard} from './active2.guard';
import { ForgotComponent } from './forgot/forgot.component';
import {MatTabsModule} from '@angular/material/tabs';
import { ForgotpassComponent } from './forgotpass/forgotpass.component';
import { OtpComponent } from './otp/otp.component';
import { CountdownModule } from 'ngx-countdown';



@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    LoginComponent,
    EventsComponent,
    EventshowComponent,
    NewuserloginComponent,
    ForgotComponent,
    ForgotpassComponent,
    OtpComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    BrowserAnimationsModule,
    FormsModule,
    RouterModule,
    ReactiveFormsModule,
    FormsModule,
    MatTabsModule,
    CountdownModule

  ],
  providers: [ActivateGuard,Active2Guard,ApiService,{provide: HTTP_INTERCEPTORS, useClass : Interceptor, multi:true }],
  bootstrap: [AppComponent]
})
export class AppModule { }