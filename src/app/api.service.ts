import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Subject } from 'rxjs';


@Injectable({
  providedIn: 'root'
})

export class ApiService {
  user: any;
  jsonapifile: string;
  private _techerMessageSource = new Subject<any>();
  private _hide = new Subject<any>();
  hide$ = this._hide.asObservable();
  techerMessage$ = this._techerMessageSource.asObservable();
  usremail: any;
  constructor(private httpClient: HttpClient) { }
  getUser(value) {
    return this.httpClient.get(`http://localhost/wordpress/wp-json/custom-plugin/login?username=${value.username}&password=${value.password}`)

  }
  sendMessage(message: any) {
    this._techerMessageSource.next(message);
  }
  isAdminRights(): boolean {
    return true;
  }
  hideButton(hide: any) {
    this._hide.next(hide);
  }

  interceptorauth(): boolean {
    this.usremail = JSON.parse(localStorage.getItem('user_email'))
    console.log('user_email', this.usremail)
    if (this.usremail == null) {
      return false;
    } else {
      return true;
    }
  }
  edit() {

    var id = localStorage.getItem('ID');
    console.log("Id *", id)

    var email = localStorage.getItem('user_email');
    console.log("user_email id", email);
    const headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    return this.httpClient.get
      (`http://localhost/wordpress/wp-json/custom-plugin/bevent?&pinged=${id}`,
        { headers })
  }
  eventsts(value) {
    this.jsonapifile = `http://localhost/wordpress/wp-json/custom-plugin/event`
    // console.log("Success",value)
    const headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    console.log('object value', value)
    var userID = localStorage.getItem('ID');
    console.log(userID);

    return this.httpClient.post(this.jsonapifile, `&pinged=${userID}&post_mime_type=${value.post_mime_type}&post_name=${value.post_name}&post_title=${value.post_title}&post_date=${value.post_date}&post_content=${value.post_content}&action=${'wp_posts'}`,
      { headers })

  }
  deletedata(id) {
    const headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
    return this.httpClient.delete(`http://localhost/wordpress/wp-json/custom-plugin/delete?ID=${id}`,
      { headers, responseType: 'text' })
  }
  emailuser(value){
    const headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
      return this.httpClient.post(`http://localhost/wordpress/wp-json/custom-plugin/emailpass?user_email=${value.user_email}`,
      { headers, responseType: 'text' })
    }
    otp(value){
      const headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
   return this.httpClient.get(`http://localhost/wordpress/wp-json/custom-plugin/otpcompare?otp=${value.otp}`,
    { headers , responseType: 'text' })
    }
}
