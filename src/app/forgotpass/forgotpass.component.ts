import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { ApiService } from '../api.service';

@Component({
  selector: 'app-forgotpass',
  templateUrl: './forgotpass.component.html',
  styleUrls: ['./forgotpass.component.css']
})
export class ForgotpassComponent implements OnInit {
  forgotpassForm: FormGroup;
  // jsonapi: string;
  update: any;

  constructor(private router: Router, private http: HttpClient, private service: ApiService) {
    this.forgotpassForm = new FormGroup({
      user_email: new FormControl('', Validators.required),
      user_pass: new FormControl('', Validators.required)
    });
  }

  ngOnInit(): void {
  }

  forgotpass(value) {
    alert("Are you sure Update data");

    this.update = `http://localhost/wordpress/wp-json/custom-plugin/forgot?user_email=${value.user_email}`
    console.log("ID", this.update)
    console.log("this is value", value.user_email);

    const headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

    return this.http.put(this.update, `user_pass=${value.user_pass}`,
      { headers, responseType: 'text' }).subscribe((data) => {
        console.log("this is new data", data)
        this.router.navigate(['login'])

      })
  }
}