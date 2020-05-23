import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { EventshowComponent } from './eventshow.component';

describe('EventshowComponent', () => {
  let component: EventshowComponent;
  let fixture: ComponentFixture<EventshowComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ EventshowComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EventshowComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
