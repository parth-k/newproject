import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router } from '@angular/router';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';

@Injectable({
  providedIn: 'root'
})
export class Active2Guard implements CanActivate {
  constructor(private apiService: ApiService, private router:Router ) { }
  canActivate(
    next: ActivatedRouteSnapshot,
    state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
      if(this.apiService.interceptorauth()){
        return true;
      }else{
        alert("this not have permition");
        this.router.navigate(['login']);
      }
  }
}
