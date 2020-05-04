import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { FormGroup, FormControl, Validators, FormBuilder } from '@angular/forms';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { ApiService } from '../api.service';

@Component({
  selector: 'app-events',
  templateUrl: './events.component.html',
  styleUrls: ['./events.component.css']
})
export class EventsComponent implements OnInit {
  Eventgroup: FormGroup;
  jsonapifile: any;
  json;
  postData: any;
  data = [];
  submitted = false;
  constructor(private router: Router, private httpClient: HttpClient, private formBuilder: FormBuilder, private service: ApiService) {
    this.Eventgroup = new FormGroup({
      post_title: new FormControl('', Validators.required),
      post_mime_type: new FormControl('', Validators.required),
      post_content: new FormControl('', Validators.required),
      post_date: new FormControl('', Validators.required),
      post_name: new FormControl('', Validators.required),
      action: new FormControl('wp_posts', Validators.required)

    });
  }
  ngOnInit(): void {
    this.Eventgroup = this.formBuilder.group({
      post_title: ['', Validators.required],
      post_mime_type: ['',[Validators.required,Validators.pattern("^((\\+91-?)|0)?[0-9]{10}$")]],
      post_name: ['', Validators.required],
      post_date: ['', Validators.required],
      post_content: ['', Validators.compose([
        Validators.required, Validators.email,
        Validators.pattern('^[a-zA-Z0-9.+-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$')
      ])],
    })
  }
  get f() { return this.Eventgroup.controls; }
  onSubmit() {
    this.submitted = true;
    if (this.Eventgroup.invalid) {
      return;
    }
  }
  events(value) {
    this.jsonapifile = `http://localhost/wordpress/wp-json/custom-plugin/event`
    // console.log("Success",value)
    const headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

    return this.httpClient.post(this.jsonapifile, `post_mime_type=${value.post_mime_type}&post_name=${value.post_name}&post_title=${value.post_title}&post_date=${value.post_date}&post_content=${value.post_content}&action=${'wp_posts'}`,
      { headers, responseType: 'text' })
      .subscribe(
        data => {
          this.data.push(data);
          console.log("POST Request is successful ", data);
        },
        error => {

          console.log("Error", error);

        }
      )

  }
}