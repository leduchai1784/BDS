import { 
  User as PrismaUser, 
  Category as PrismaCategory, 
  Project as PrismaProject, 
  Property as PrismaProperty, 
  PropertyImage as PrismaPropertyImage, 
  Wishlist as PrismaWishlist, 
  Appointment as PrismaAppointment, 
  AiCampaign as PrismaAiCampaign 
} from '@prisma/client'

export type User = PrismaUser
export type Category = PrismaCategory
export type Project = PrismaProject
export type Property = PrismaProperty
export type PropertyImage = PrismaPropertyImage
export type Wishlist = PrismaWishlist
export type Appointment = PrismaAppointment
export type AiCampaign = PrismaAiCampaign

// Relations and Extended Types
export interface ExtendedProperty extends Property {
  owner: User
  category?: Category | null
  project?: Project | null
  propertyImages: PropertyImage[]
  appointments?: Appointment[]
  wishlists?: Wishlist[]
  aiCampaigns?: AiCampaign[]
}

export interface ExtendedAppointment extends Appointment {
  user?: User | null
  property: Property & {
    owner: User
    propertyImages: PropertyImage[]
  }
}

export interface ExtendedWishlist extends Wishlist {
  user: User
  property: Property & {
    propertyImages: PropertyImage[]
  }
}
