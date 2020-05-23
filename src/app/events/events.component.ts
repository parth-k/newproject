import { Component, OnInit, Output, EventEmitter } from '@angular/core';
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
  last_id: any;
  obj: any = [];
  submitted = false;
  prmid: any;
  update: any;
  isShow: boolean = false;
  isButtonVisible: boolean;
  // private isButtonVisible = false;

  constructor(private router: Router, private httpClient: HttpClient, private formBuilder: FormBuilder, private service: ApiService) {
    this.Eventgroup = new FormGroup({
      id: new FormControl(''),
      post_title: new FormControl('', Validators.required),
      post_mime_type: new FormControl('', Validators.required),
      post_content: new FormControl('', Validators.required),
      post_date: new FormControl('', Validators.required),
      post_name: new FormControl('', Validators.required),
      action: new FormControl('wp_posts', Validators.required)
    });
  }
  ngOnInit(): void {
    this.service.techerMessage$.subscribe(message => {
      console.log("this is message", message);
      this.obj = message;
      console.log("msg", this.obj)
    })

    this.Eventgroup = this.formBuilder.group({
      post_title: ['', Validators.required],
      post_mime_type: ['', [Validators.required, Validators.pattern("^((\\+91-?)|0)?[0-9]{10}$")]],
      post_name: ['', Validators.required],
      post_date: ['', Validators.required],
      post_content: ['', Validators.compose([
        Validators.required, Validators.email,
        Validators.pattern('^[a-zA-Z0-9.+-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$')
      ])],
    })

    this.service.hide$
    .subscribe(
      hide => {
        console.log('this is bool value', hide)
        this.isShow = hide;
        this.isButtonVisible = true;
      }
    )
  }
  get f() { return this.Eventgroup.controls; }
  onSubmit() {
    this.submitted = true;
    if (this.Eventgroup.invalid) {
      return;
    }
  }
  events(value) {
    this.service.eventsts(value).subscribe(
      data => {
        this.data.push(value);
        console.log("POST Request is successfull ", data);
        value.id = data
        console.log('this is prm id ====>', value.id);
        localStorage.setItem('prmid', JSON.stringify(value.id));
        this.Eventgroup.reset();
      },
      error => {

        console.log("Error", error);

      }
    )
  }
  updatedatanew(value) {
    alert("Are you sure Update data");
    console.log("this is value", value.id);
    this.prmid = JSON.parse(localStorage.getItem('prmid'));
    console.log("primary id", this.prmid);

    this.update = `http://localhost/wordpress/wp-json/custom-plugin/update?ID=${this.prmid}`
    console.log("ID", this.update)

    const headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

    return this.httpClient.put(this.update, `&post_title=${value.post_title}&post_mime_type=${value.post_mime_type}
    &post_name=${value.post_name}&post_date=${value.post_date}&post_content=${value.post_content}`,
      { headers, responseType: 'text' }).subscribe((data) => {
        console.log("this is new data", data)
        this.isShow = false
        this.isButtonVisible = false;
        this.Eventgroup.reset();
      })
  }

}