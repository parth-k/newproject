import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { NewuserloginComponent } from './newuserlogin.component';

describe('NewuserloginComponent', () => {
  let component: NewuserloginComponent;
  let fixture: ComponentFixture<NewuserloginComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ NewuserloginComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(NewuserloginComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
