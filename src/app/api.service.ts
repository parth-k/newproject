import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';


@Injectable({
  providedIn: 'root'
})

export class ApiService {
  constructor(private httpClient: HttpClient) {}
  getUser(){
    // console.log("service calleed");
    return this.httpClient.get('http://localhost/wordpress/wp-json/custom-plugin/login?');
  } 
}
