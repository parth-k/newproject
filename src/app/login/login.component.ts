import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { ApiService } from '../api.service';
@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {

loginForm: FormGroup;
  jsonapifile : any 
  constructor(private router: Router, private http:HttpClient ,private service:ApiService) {
    this.loginForm = new FormGroup({
      username: new FormControl('', Validators.required),
      password: new FormControl('', Validators.required)
    });
  }

  loginUser(value) {
    this.http.get(`http://localhost/wordpress/wp-json/custom-plugin/login?username=${value.username}&password=${value.password}`)
    .subscribe(data => {
      this.jsonapifile = data
      console.log("Success", this.jsonapifile),
      this.router.navigate(['home'], { state: value }) 

    },
    error => {
      console.error(" Error ", error)
    })
  }
}