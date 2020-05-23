import { Component, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { ApiService } from '../api.service';
import { CountdownComponent } from 'ngx-countdown';


@Component({
  selector: 'app-otp',
  templateUrl: './otp.component.html',
  styleUrls: ['./otp.component.css']
})
export class OtpComponent implements OnInit {
  OTPForm: FormGroup;
  jsonapi: string;
  timerLeftTime: any;
  config = {
    leftTime: 100,
    size:'large' 
  }

  @ViewChild('countdown',{ static:false })private countdown: CountdownComponent;
  constructor(private router: Router, private http: HttpClient, private service: ApiService) {
    this.OTPForm = new FormGroup({
      otp: new FormControl('', Validators.required),
      action: new FormControl('wp_users')
    });
  }

  ngOnInit(): void {
   this.config.leftTime = JSON.parse(localStorage.getItem('second'))
  }

  firstEvent(e: Event) {
    if (e["action"] == "done") {
      this.router.navigate(['login'])
    }
    let data = e
    this.timerLeftTime = e['left']
    console.log(data)
    setInterval((data) => {
      localStorage.setItem("second", JSON.stringify(this.timerLeftTime / 1000));
      console.log(" ***** ", this.timerLeftTime / 1000)
      this.checkTime(this.timerLeftTime)
    }, 1000)
  }
  checkTime(data) {
    console.log(data / 1000);
    this.timerLeftTime = data - 1000
  }
  Done(value) {
    this.service.otp(value).subscribe(data => {
      console.log("this OTP is varify", data);
      this.router.navigate(['forgotpass'])
    },
      error => {
        console.log("Error", error);
      }
    )
  }


  timerColor(){
    return{
    'onemin': this.timerLeftTime <= 60000,
    'sec': this.timerLeftTime <= 30000,
    }
  }
}
