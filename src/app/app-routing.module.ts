import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { HomeComponent } from './home/home.component';
import { EventsComponent } from './events/events.component';
import { from } from 'rxjs';
import {ActivateGuard} from './activate.guard';
import {Active2Guard} from './active2.guard';
import {EventshowComponent} from './eventshow/eventshow.component';
import  {NewuserloginComponent } from './newuserlogin/newuserlogin.component';
import { ForgotComponent } from './forgot/forgot.component';
import { OtpComponent } from './otp/otp.component';
import { ForgotpassComponent } from './forgotpass/forgotpass.component';


const routes: Routes = [
  { path: 'login', component: LoginComponent,pathMatch: 'full' },
  { path: 'home', component: HomeComponent, pathMatch: 'full',canActivate:[ActivateGuard] },
  { path: 'events', component: EventsComponent, pathMatch: 'full',canActivate:[Active2Guard] },
  { path: 'eventshow', component: EventshowComponent, pathMatch: 'full',canActivate:[Active2Guard] },
  { path: 'newuserlogin', component: NewuserloginComponent, pathMatch: 'full' },
  { path: 'forgot', component: ForgotComponent, pathMatch: 'full' },
  { path: 'otp', component: OtpComponent, pathMatch: 'full' },
  { path: 'forgotpass', component: ForgotpassComponent, pathMatch: 'full' }






];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }