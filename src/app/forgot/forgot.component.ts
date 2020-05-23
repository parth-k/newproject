import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { ApiService } from '../api.service';
@Component({
  selector: 'app-forgot',
  templateUrl: './forgot.component.html',
  styleUrls: ['./forgot.component.css']
})
export class ForgotComponent implements OnInit {
  forgotForm: FormGroup;   
  jsonapi: string;
  constructor(private router: Router, private http: HttpClient, private service: ApiService) {
    this.forgotForm = new FormGroup({
      user_email: new FormControl('', Validators.required),
      // user_pass: new FormControl('', Validators.required)
    });
  }

  ngOnInit(): void {
  }

  forgotUser(value) {
    this.jsonapi = `http://localhost/wordpress/wp-json/custom-plugin/forgototp`
    // this.jsonapi = `http://localhost/wordpress/wp-json/custom-plugin/forgot`
    const headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    console.log('object ', value)
    return this.http.post(
      this.jsonapi,
      `user_email=${value.user_email}&action=${'otp'}`,
      // `user_email=${value.user_email}&user_pass=${value.user_pass}&action=${'wp_users'}`,
      { headers, responseType: 'text' }
    )
      .subscribe(
        data => {
          console.log("POST Request is successful", data);
          this.router.navigate(['otp'])
        },
        error => {
          console.log("Error", error);
        }
      )
  }
}
