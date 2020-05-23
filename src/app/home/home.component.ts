import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {

  constructor(public http:HttpClient) { }
  jsonapifile : any ;

  ngOnInit(): void {}
  logout(){
    this.http.get('http://localhost/wordpress/wp-json/custom-plugin/logout')
    .subscribe(data => {
      this.jsonapifile = data
      localStorage.removeItem('ID');
      localStorage.removeItem('user_email');
    })
  } 
}