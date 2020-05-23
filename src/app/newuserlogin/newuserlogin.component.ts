import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { ApiService } from '../api.service';


@Component({
  selector: 'app-newuserlogin',
  templateUrl: './newuserlogin.component.html',
  styleUrls: ['./newuserlogin.component.css']
})
export class NewuserloginComponent implements OnInit {

  newuserForm:FormGroup;

  constructor(private router: Router, private http:HttpClient ,private service:ApiService) { 
    this.newuserForm = new FormGroup({
      user_login : new FormControl(''),
      user_email: new FormControl('', Validators.required),
      user_pass: new FormControl('', Validators.required),
      user_nicename: new FormControl('', Validators.required)
    });
   }

  ngOnInit(): void {
  }

  newuser(value){
    const headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    this.http.post(`http://localhost/wordpress/wp-json/custom-plugin/newuser`,`&user_login=${value.user_login}&user_pass=${value.user_pass}
    &user_email=${value.user_email}&user_nicename=${value.user_nicename}`,
    { headers , responseType: 'text' })
    .subscribe((data) =>{
      console.log("this is new user",data)
      this.router.navigate(['login']);
    })
    this.service.emailuser(value).subscribe(data =>{
      // console.log("this is user id" ,data)
    })
  }
}
