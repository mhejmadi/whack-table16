module Counter exposing (main)

import Html exposing (Html, div, text, button, input)
import Html.Attributes exposing (..)
import Html.Events exposing (onClick, onInput)


type alias Model =
  { review : String
  , title : String
  , username : String
  , reviews : List String
  }


model : Model
model =
  { review = ""
  , title = "New Review"
  , username = "Your User Name"
  , reviews = []
  }


type Msg
  = UpdateReview String
    | UpdateTitle String
    | UpdateUsername String
    | SaveReview


update : Msg -> Model -> ( Model, Cmd Msg )
update msg model =
  case msg of
    UpdateReview newReview ->
      ( { model | review = newReview }
      , Cmd.none )

    UpdateUsername newName ->
      ( { model | username = newName }
      , Cmd.none )

    UpdateTitle newTitle ->
      ( { model | title = newTitle }
      , Cmd.none )

    SaveReview ->
      ( { model | title = "", username = "", review = "", reviews = model.title :: model.reviews }
      , Cmd.none )


view : Model -> Html Msg
view model =
  div []
    [ div [] (List.map text model.reviews )
    , input [ class "example", value model.title, onInput UpdateTitle, placeholder "Title" ] [ ]
    , input [ value model.username, onInput UpdateUsername, placeholder "User Name" ] [ ]
    , input [ value model.review, onInput UpdateReview, placeholder "Review" ] [ ]
    , button [ onClick SaveReview ] [ text "Save" ]
    , text "Title: "
    , text model.title
    , text "Review: "
    , text model.review
    , text "User: "
    , text model.username
    ]


main : Program Never Model Msg
main =
  Html.program
    { view = view
    , update = update
    , subscriptions = \_ -> Sub.none
    , init = ( model, Cmd.none )
    }
