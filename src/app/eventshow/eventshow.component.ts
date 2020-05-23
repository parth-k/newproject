import { Component, OnInit, Input, EventEmitter, Output } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { ApiService } from '../api.service';


@Component({
  selector: 'app-eventshow',
  templateUrl: './eventshow.component.html',
  styleUrls: ['./eventshow.component.css']
})
export class EventshowComponent implements OnInit {
  @Input() MSG: object;
  isShow = false;
  isButtonVisible: boolean;
  btnDisabled = false;
  @Output() exampleOutput = new EventEmitter<string>();


  exampleChild: string = 'hello abc';
  confirmed: any = [];
  pending: any = [];
  canceled: any = [];
  exampleMethodChild() {
    this.exampleOutput.emit(this.exampleChild);
  }
  send_data: any
  events: any
  api: any;
  // booking: any = [];
  constructor(public http: HttpClient, private service: ApiService) {
    this.showevent()

  }
  bevent: any = [];
  // abc:any;


  ngOnInit(): void { }
  showevent() {
    this.service.edit().subscribe((data) => {
      this.events = data
      console.log('this is api response', this.events);
      this.events.forEach((element) => {
        console.log("elements", element)
        if (element.post_status == 'sln-b-confirmed') {
          this.confirmed.push(element)
          this.btnDisabled = true;
        } else if (element.post_status == 'Pending') {
          this.pending.push(element)
        } else if (element.post_status == 'sln-b-canceled') {
          this.canceled.push(element)
          this.btnDisabled = true;
        }
      })
    });
  }
  update(item) {
    console.log(" item ", item);
    this.service.sendMessage(item);
    this.service.hideButton(this.isShow);
    this.service.hideButton(this.isButtonVisible)
    localStorage.setItem('prmid', JSON.stringify(item.id));
    // console.log("primary id",prmid);
  };
  delete(id) {
    console.log("Id ====>", id)
    this.service.deletedata(id).subscribe(data => {
      this.showevent();
    }
    )
  }
}
